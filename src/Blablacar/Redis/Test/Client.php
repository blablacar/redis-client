<?php

namespace Blablacar\Redis\Test;

use Blablacar\Redis\Client as BaseClient;

/**
 * Client
 *
 * The only purpose of this class is to be used to mock the default Client with
 * prophecy which can mock magic methods like __call
 */
class Client extends BaseClient
{
    // Keys
    public function del() {}
    public function dump() {}
    public function exists() {}
    public function expire() {}
    public function expireat() {}
    public function keys() {}
    public function migrate() {}
    public function move() {}
    public function persist() {}
    public function pexpire() {}
    public function pexpireat() {}
    public function pttl() {}
    public function randomkey() {}
    public function rename() {}
    public function renamenx() {}
    public function restore() {}
    public function sort() {}
    public function ttl() {}
    public function type() {}
    public function scan() {}

    // Strings
    public function append() {}
    public function bitcount() {}
    public function bitop() {}
    public function bitpos() {}
    public function decr() {}
    public function decrby() {}
    public function get() {}
    public function getbit() {}
    public function getrange() {}
    public function getset() {}
    public function incr() {}
    public function incrby() {}
    public function incrbyfloat() {}
    public function mget() {}
    public function mset() {}
    public function msetnx() {}
    public function psetex() {}
    public function set() {}
    public function setbit() {}
    public function setex() {}
    public function setnx() {}
    public function setrange() {}
    public function strlen() {}

    // Hashes
    public function hdel() {}
    public function hexists() {}
    public function hget() {}
    public function hgetall() {}
    public function hincrby() {}
    public function hincrbyfloat() {}
    public function hkeys() {}
    public function hlen() {}
    public function hmget() {}
    public function hmset() {}
    public function hset() {}
    public function hsetnx() {}
    public function hvals() {}
    public function hscan($key, &$iterator, $pattern = null, $count = 0) {}

    // Lists
    public function blpop() {}
    public function brpop() {}
    public function brpoplpush() {}
    public function lindex() {}
    public function linsert() {}
    public function llen() {}
    public function lpop() {}
    public function lpush() {}
    public function lpushx() {}
    public function lrange() {}
    public function lrem() {}
    public function lset() {}
    public function ltrim() {}
    public function rpop() {}
    public function rpoplpush() {}
    public function rpush() {}
    public function rpushx() {}

    // Sets
    public function sadd() {}
    public function scard() {}
    public function sdiff() {}
    public function sdiffstore() {}
    public function sinter() {}
    public function sinterstore() {}
    public function sismember() {}
    public function smembers() {}
    public function smove() {}
    public function spop() {}
    public function srandmember() {}
    public function srem() {}
    public function sunion() {}
    public function sunionstore() {}
    public function sscan() {}
    
    // Flush
    public function flushDb() {}  
}
