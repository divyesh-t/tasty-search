<?php
/**
 * Created by PhpStorm.
 * User: divyesh.t
 * Date: 10/04/17
 * Time: 18:33
 */

namespace App\Services;

use App\Models\TokenDocMappingModel;


/**
 * Class MyQueryMinHeap
 *
 * To store docs in min heap, with their count (sum of keywords found) and review score
 * @package App\Services
 */
class MyQueryMinHeap extends \SplMinHeap
{
    /**
     * @param mixed $value1
     * @param mixed $value2
     * @return mixed
     */
    public function  compare($value1, $value2 ) {
        if ($value1['count'] === $value2['count']) {
            return ($value2['score'] - $value1['score']);
        }
        return ($value2['count'] - $value1['count']);
    }
}

/**
 * Class Search
 *
 * To search keywords from docs
 * @package App\Services
 */
class Search
{
    /**
     *
     * To search keywords in docs and returning top k performing docs
     * @param $queries  array array of keywords
     * @param $k    int    top performing docs
     * @return array
     */
    public static function findTopDocs($queries, $k)
    {
        $resultArr = [];
        $qLen = count($queries);
        // build minheap of size k to keep track of max performing docs
        $myHeap = new MyQueryMinHeap();

        // fetch associated docIds for each query words and keep count of query words found in each doc
        foreach ($queries as $query) {
            $res = (TokenDocMappingModel::getDocsForToken($query));
            foreach ($res as $docId) {
                $resultArr[$docId] = ( $resultArr[$docId] ?? 0) + 1;
            }
        }
        $countR = 0;

        // build and maintain heap while iterating through all docs
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
            // to get top k docs with id
            $k = min($k , $myHeap->count());
            for($i = 0; $i < $k; $i++) {
                $doc = $myHeap->extract();
                $res[$k - $i - 1] = $doc;
                $id = substr($doc['docId'],0, strlen($doc['docId']) - 2);
                $ids[] = $id;
                $idToResMapping[$id] = $k - $i - 1;
            }
        // fetch from database
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