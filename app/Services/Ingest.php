<?php

/**
 * Created by PhpStorm.
 * User: divyesh.t
 * Date: 05/04/17
 * Time: 15:46
 */
class IngestDoc
{

    public static function ingestToRedis($doc)
    {
        $client = \Redis::connection();
        $docId;
        $words = array_merge(
            str_word_count($doc['review']['summary'], 1),
            str_word_count($doc['review']['text'])  , 1);
        foreach ($words as $word) $client->sadd($docId, $word);

    }


}