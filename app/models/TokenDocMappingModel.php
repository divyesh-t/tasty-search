<?php

namespace App\Models;
use App\Services\Ingest;
use Illuminate\Support\Facades\DB;

class TokenDocMappingModel
{
    const TABLE = 'DOCUMENTS';

    public static function getDocsForToken($token) {
        $client = \Redis::connection();
        return $client->smembers(Ingest::KEY_PREFIX.$token);
    }

    public static function getDocsForTokens($tokens)
    {
        $ret = array_keys($tokens);
        foreach ($tokens as $token) {
            $ret[$token] = self::getDocsForToken($token);
        }
        return $ret;
    }

    public static function getDocsById($ids)
    {
        return DB::table(self::TABLE)->whereIn('id', $ids)->select('productId', 'userId', 'profileName', 'helpfulness', 'score', 'time', 'summary', 'text', 'id')->get();
    }
    public static function storeDocAndGetId($doc)
    {
        return DB::table(self::TABLE)->insertGetId($doc);
    }
}