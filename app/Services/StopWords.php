<?php
/**
 * Created by PhpStorm.
 * User: divyesh.t
 * Date: 08/04/17
 * Time: 16:29
 */

namespace App\Services;


class StopWords
{
    const REDIS_KEY = 'stopWordsTrie';
    const FILE_NAME = 'stop_words.txt';
    public static function getStopWordsTrie($rebuild = false) {
        $client = \Redis::connection();
        $trie = $client->get(self::REDIS_KEY);
        if (empty($trie) || $rebuild) {
            $trie = self::buildStopWordsTrie();
        } else {
            $trie = unserialize($trie);
        }
        return $trie;
    }

    private static function buildStopWordsTrie()
    {
        $fp = fopen(resource_path(self::FILE_NAME),'r');
        if (!$fp) return new Trie(null);
        $trie = new Trie();
        $count = 0;
        while(( $word = self::getNextStopWord($fp)) !== false) {
            $trie->add($word);
            $count++;
        }
        self::storeStopWordsTrieToRedis($trie);
        return $trie;
    }

    private static function storeStopWordsTrieToRedis($trie)
    {
        $client = \Redis::connection();
        $client->set(self::REDIS_KEY, serialize($trie));
    }

    private static function getNextStopWord($fp)
    {
        $line = fgets($fp);
        if (false === $line) return $line;
        return trim($line);
    }

}