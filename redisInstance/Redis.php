<?php
/**
 * Created by PhpStorm.
 * User: dy
 * Date: 2018/8/20
 * Time: 9:16
 */
namespace redis;
class Redis
{
    private static $redis = null;
    private static $host = '127.0.0.1';
    private static $password = null;
    public static function getRedis($select = null)
    {
        if(self::$redis === null)
        {
            self::$redis = new \Redis();
            self::$redis->connect(self::$host);
            if(!empty(self::$password))
            {
                self::$redis->auth(self::$password);
            }
        }
        if(!empty($select))
        {
            self::$redis->select($select);
        }
        return self::$redis;
    }

    private function __construct(){}
    private function __clone(){}
}