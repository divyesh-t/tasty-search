<?php

namespace App\Services;


/**
 * Class StopWords
 * @package App\Services
 */
class StopWords
{
    /**
     *  key name by which storing stop words trie in redis
     */
    const REDIS_KEY = 'stopWordsTrie';
    /**
     *  File name in which stop words are stored, file must be in resource directory
     */
    const FILE_NAME = 'stop_words.txt';

    /**
     *  function to get stop words trie,
     *      looks into redis if present or not
     *          if not present build from file and store in redis as well.
     * @param bool $rebuild rebuild trie, irrespective of in redis or not
     * @return Trie|mixed
     */
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

    /**
     *  to build trie of stopwords from file nd store in redis
     * @return Trie
     */
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

    /**
     * To store trie in redis
     * @param $trie
     */
    private static function storeStopWordsTrieToRedis($trie)
    {
        $client = \Redis::connection();
        $client->set(self::REDIS_KEY, serialize($trie));
    }

    /**
     * @param $fp
     * @return string
     */
    private static function getNextStopWord($fp)
    {
        $line = fgets($fp);
        if (false === $line) return $line;
        return trim($line);
    }

}