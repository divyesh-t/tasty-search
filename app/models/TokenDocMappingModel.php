<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * Class TokenDocMappingModel
 * To dealt with database storing docs and redis storing inverted indices
 * @package App\Models
 */
class TokenDocMappingModel
{
    /**
     *  Table name whered docs are stored
     */
    const TABLE = 'DOCUMENTS';

    /**
     *  key prefix to prepend while storing in redis
     */
    const KEY_PREFIX = 'ts:';

    /**
     * @param $token
     * @return mixed
     */
    public static function getDocsForToken($token) {
        return \Redis::connection()->smembers(self::KEY_PREFIX.$token);
    }

    /**
     * @param $tokens
     * @return array
     */
    public static function getDocsForTokens($tokens)
    {
        $ret = array_keys($tokens);
        foreach ($tokens as $token) {
            $ret[$token] = self::getDocsForToken($token);
        }
        return $ret;
    }

    /**
     * @param $ids
     * @return mixed
     */
    public static function getDocsById($ids)
    {
        return DB::table(self::TABLE)->whereIn('id', $ids)
            ->select('productId', 'userId', 'profileName', 'helpfulness', 'score', 'time', 'summary', 'text', 'id')
            ->get();
    }

    /**
     * @param $doc
     * @return mixed
     */
    public static function storeDocAndGetId($doc)
    {
        return DB::table(self::TABLE)->insertGetId($doc);
    }

    /**
     * @param $word
     * @param $docIdWithScore
     */
    public static function ingestToRedis($word, $docIdWithScore)
    {
        \Redis::connection()->sadd(self::KEY_PREFIX . $word, $docIdWithScore);
    }

    /**
     * To delete stored invert index from redis
     * @return string
     */
    public static function deleteAllIngestedKeysFromRedis()
    {
        $bash = 'redis-cli --scan --pattern "' . self::KEY_PREFIX . '*" | xargs -L 1000 redis-cli DEL';

        return @shell_exec($bash);
    }

    public static function getWords()
    {
        return array_map(function($str) {
                return substr($str, strlen(self::KEY_PREFIX));
            },\Redis::connection()->keys(self::KEY_PREFIX . '*')
        );
    }
}