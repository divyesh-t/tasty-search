<?php

namespace App\Services;
use App\Models\TokenDocMappingModel;


class MyQueryMinHeap extends \SplMinHeap
{
    public function  compare($value1, $value2 ) {
        if ($value1['count'] === $value2['count']) {
            return ($value2['score'] - $value1['score']);
        }
        return ($value2['count'] - $value1['count']);
    }
}

class Ingest
{

    const KEY_PREFIX = 'ts:';
    private static $stopWordsTrie;

    private static function getStopWordsTrie()
    {
        if (empty(self::$stopWordsTrie)) self::$stopWordsTrie = StopWords::getStopWordsTrie();
        return self::$stopWordsTrie;
    }
    public static function ingestToRedis($docId, $review, $score)
    {
        $client = \Redis::connection();
        $trie = self::getStopWordsTrie();
        $words = array_merge(
            str_word_count($review['summary'], 1),
            str_word_count($review['text'], 1));
        foreach ($words as $word) {
            if ($trie->search($word) !== null) continue;
            $client->sadd(self::KEY_PREFIX.strtolower($word), $docId.':'.$score);
        }

    }

    public static function ingestDocs($filename, $totalDocsToIngest = 10000, $progressBar = null)
    {
        $fp = fopen($filename, 'r');
        for($count = 0;$count < $totalDocsToIngest; $count++ ) {
            $doc = static::getNextDoc($fp);
            if (empty($doc)) break;
            $docId = TokenDocMappingModel::storeDocAndGetId($doc);
            self::ingestToRedis($docId, $doc, $doc['score']);
            if ($progressBar && $count%1000 == 0) $progressBar->advance(1000);
        }
        return $count;
    }

    private static function getNextDoc($fp)
    {
        $data = [];
        $data['productId']   = trim(substr(fgets($fp), 19));
        $data['userId']      = trim(substr(fgets($fp), 15));
        $data['profileName'] = mb_convert_encoding(trim(substr(fgets($fp), 20)), 'UTF-8');
        $data['helpfulness'] = mb_convert_encoding(trim(substr(fgets($fp), 20)), 'UTF-8');
        $data['score']       = floatval(substr(fgets($fp), 14));
        $data['time']        = intval(substr(fgets($fp), 13));
        $data['summary']     = mb_convert_encoding(trim(substr(fgets($fp), 16)), 'UTF-8');
        $data['text']        = mb_convert_encoding(trim(substr(fgets($fp), 13)), 'UTF-8');
        fgets($fp); // for new line
        return $data;
    }

    public static function findTopDocs($queries, $k)
    {
        $resultArr = [];
        $qLen = count($queries);
        $myHeap = new MyQueryMinHeap();
        foreach ($queries as $query) {
            $res = (TokenDocMappingModel::getDocsForToken($query));
            foreach ($res as $docId) {
                if (!isset($resultArr[$docId])) $resultArr[$docId] = 0;
                $resultArr[$docId]++;
            }

        }
        $countR = 0;
        foreach ($resultArr as $res => $count) {
            if ($countR <= $k) $myHeap->insert(['count' => $count, 'docId' => $res, 'score' => floatval($res[strlen($res) - 1])]);
            else {
                $top = $myHeap->top();
                $shouldAdd = ['count' => $count, 'docId' => $res, 'score' => floatval($res[strlen($res) - 1])];
                if ($myHeap->compare($top, $shouldAdd) > 0){
                    $myHeap->extract();
                    $myHeap->insert($shouldAdd);
                }
            }
            $countR++;
        }
        $res = [];
        $idToResMapping = [];
        $ids = [];
        if (!$myHeap->isEmpty())
            for($i = 0; $i < $k; $i++) {
                $doc = $myHeap->extract();
                $res[$k - $i - 1] = $doc;
                $id = substr($doc['docId'],0, strlen($doc['docId']) - 2);
                $ids[] = $id;
                $idToResMapping[$id] = $k - $i - 1;
            }
        $docs = TokenDocMappingModel::getDocsById($ids);
        $resultArr = [];
        foreach ($docs as $doc) {
            $ind = $idToResMapping[$doc->id];
            $resultArr[$ind]   = $doc;
            $resultArr[$ind]->matchingScore = floatval($res[$ind]['count'] / $qLen);
            $resultArr[$ind]->rank  = ($ind + 1);
        }
        return $resultArr;
    }
}