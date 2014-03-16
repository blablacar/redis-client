<?php

namespace Blablacar\Redis;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_initializable()
    {
        $this->assertInstanceOf('Blablacar\Redis\Client', new Client());
    }

    public function test_connect_create_a_redis_connection()
    {
        $client = new Client();
        $this->assertNull($client->getRedis());
        $client->connect();
        $this->assertInstanceOf('\Redis', $client->getRedis());
    }

    public function test_call_a_method_create_a_redis_connection()
    {
        $client = new Client();
        $this->assertNull($client->getRedis());
        $return = $client->get('foo.bar');
        $this->assertInstanceOf('\Redis', $client->getRedis());
        $this->assertFalse($return);
    }

    public function test_set_get()
    {
        $client = new Client();
        $this->assertEquals(1, $client->set('foobar', 42));
        $this->assertEquals(42, $client->get('foobar'));
    }
}
