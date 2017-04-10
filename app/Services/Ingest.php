<?php

namespace App\Services;
use App\Models\TokenDocMappingModel;


/**
 * Class Ingest
 * @package App\Services
 */
class Ingest
{

    /**
     * To store trie of stop words to exclude while indexing
     * @var
     */
    private static $stopWordsTrie;

    /**
     * To get stop words tire, if not present, initialize it.
     * @return Trie|mixed
     */
    private static function getStopWordsTrie()
    {
        if (empty(self::$stopWordsTrie)) self::$stopWordsTrie = StopWords::getStopWordsTrie();
        return self::$stopWordsTrie;
    }


    /**
     * To ingest words for particular doc
     * @param $docId
     * @param $review
     * @param $score
     */
    public static function ingest($docId, $review, $score)
    {
        $trie = self::getStopWordsTrie();
        $words = array_merge(
            str_word_count($review['summary'], 1),
            str_word_count($review['text'], 1)
        );
        foreach ($words as $word) {
            if ($trie->search($word) !== null) continue;
            TokenDocMappingModel::ingestToRedis(strtolower($word), $docId.':'.$score);
        }
    }

    /**
     * To ingest docs from given file
     * @param $filename
     * @param int $totalDocsToIngest
     * @param null $progressBar (for commands use only)
     * @return int
     */
    public static function ingestDocs($filename, $totalDocsToIngest = 10000, $progressBar = null)
    {
        $fp = fopen($filename, 'r');
        for($count = 0;$count < $totalDocsToIngest; $count++ ) {
            $doc = static::getNextDoc($fp);
            if (empty($doc)) break;
            $docId = TokenDocMappingModel::storeDocAndGetId($doc);
            self::ingest($docId, $doc, $doc['score']);
            if ($progressBar && $count%1000 == 0) $progressBar->advance(1000);
        }
        return $count;
    }

    /**
     *  To get next doc from file
     * @param $fp
     * @return array
     */
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
}