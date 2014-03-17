<?php

namespace Blablacar\Test;

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
    public function expire() {}
    public function exists() {}

    // Strings
    public function get() {}
    public function set() {}
    public function setex() {}
    public function setnx() {}

    // Sets
    public function sadd() {}
    public function scard() {}
}
