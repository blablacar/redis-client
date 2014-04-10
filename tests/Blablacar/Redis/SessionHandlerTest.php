<?php

namespace Blablacar\Redis;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    protected function clientLock(ObjectProphecy $client)
    {
        $client->setnx(Argument::type('string'), Argument::exact(1))->willReturn(true);
        $client->expire(Argument::type('string'), Argument::exact(30001))->willReturn(true);
    }

    public function test_it_is_initializable()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $this->assertInstanceOf(
            'Blablacar\Redis\SessionHandler',
            new SessionHandler($client->reveal())
        );
    }

    public function test_read_without_key()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $this->clientLock($client);
        $client->get(Argument::exact('session:foobar'))->willReturn(false);

        $handler = new SessionHandler($client->reveal());

        $this->assertEquals('', $handler->read('foobar'));
    }

    public function test_read_with_key()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $this->clientLock($client);
        $client->get(Argument::exact('session:foobar'))->willReturn('foobar');

        $handler = new SessionHandler($client->reveal());

        $this->assertEquals('foobar', $handler->read('foobar'));
    }

    public function test_write_with_ttl()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $this->clientLock($client);
        $client->setex(
            Argument::type('string'),
            Argument::exact(1200),
            Argument::type('string')
        )->will(function ($args) {
            $this->get($args[0])->willReturn($args[2])->shouldBeCalledTimes(1);

            return true;
        })->shouldBeCalledTimes(1);
        $client->del(
            Argument::type('string')
        )->willReturn(true)
        ->shouldBeCalledTimes(1);

        $handler = new SessionHandler($client->reveal(), 'session', 1200);

        $this->assertTrue($handler->write('key', 'value'));
        $this->assertEquals('value', $handler->read('key'));
    }

    public function test_write_without_ttl()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $this->clientLock($client);
        $client->set(
            Argument::type('string'),
            Argument::type('string')
        )->will(function ($args) {
            $this->get($args[0])->willReturn($args[1])->shouldBeCalledTimes(1);

            return true;
        })->shouldBeCalledTimes(1);
        $client->del(
            Argument::type('string')
        )->willReturn(true)
        ->shouldBeCalledTimes(1);

        $handler = new SessionHandler($client->reveal());

        $this->assertTrue($handler->write('key', 'value'));
        $this->assertEquals('value', $handler->read('key'));
    }

    public function test_write_when_session_is_locked()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Test\Client');
        $client->setex(
            Argument::type('string'),
            Argument::exact(3600),
            Argument::type('string')
        )->will(function ($args) {
            $this->get($args[0])->willReturn($args[2])->shouldBeCalledTimes(1);

            return true;
        })->shouldBeCalledTimes(1);
        $client->setnx(
            Argument::exact('session:key1.lock'),
            Argument::exact(1)
        )->willReturn(true)->shouldBeCalledTimes(2);
        $client->setnx(
            Argument::exact('session:key2.lock'),
            Argument::exact(1)
        )->willReturn(false)->shouldBeCalledTimes(3);
        $client->expire(
            Argument::type('string'),
            Argument::exact(451)
        )->willReturn(true);
        $client->del(
            Argument::type('string')
        )->willReturn(true)
        ->shouldBeCalledTimes(1);

        $handler = new SessionHandler($client->reveal(), 'session', 3600, 150000, 450000);

        $this->assertTrue($handler->write('key1', 'value'));
        $this->assertEquals('value', $handler->read('key1'));

        $this->setExpectedException('Blablacar\Redis\Exception\LockException');
        $handler->write('key2', 'value');
    }
}
