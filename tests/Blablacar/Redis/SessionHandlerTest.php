<?php

namespace Blablacar\Redis;

use Blablacar\Redis\SessionHandler;
use Prophecy\Argument;

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

    public function test_it_is_initializable()
    {
        $client = $this->prophet->prophesize('Blablacar\Test\Client');
        $this->assertInstanceOf(
            'Blablacar\Redis\SessionHandler',
            new SessionHandler($client->reveal())
        );
    }

    public function test_read_without_key()
    {
        $client = $this->prophet->prophesize('Blablacar\Test\Client');
        $client->get(Argument::exact('session:foobar'))->willReturn(false);

        $handler = new SessionHandler($client->reveal());

        $this->assertNull($handler->read('foobar'));
    }

    public function test_read_with_key()
    {
        $client = $this->prophet->prophesize('Blablacar\Test\Client');
        $client->get(Argument::exact('session:foobar'))->willReturn('foobar');

        $handler = new SessionHandler($client->reveal());

        $this->assertEquals('foobar', $handler->read('foobar'));
    }

    public function test_write_with_ttl()
    {
        $client = $this->prophet->prophesize('Blablacar\Test\Client');
        $client->setex(
            Argument::type('string'),
            Argument::exact(1200),
            Argument::type('string')
        )->will(function ($args) {
            $this->get($args[0])->willReturn($args[2])->shouldBeCalledTimes(1);

            return true;
        })->shouldBeCalledTimes(1);

        $handler = new SessionHandler($client->reveal(), 'session', 1200);

        $this->assertTrue($handler->write('key', 'value'));
        $this->assertEquals('value', $handler->read('key'));
    }

    public function test_write_without_ttl()
    {
        $client = $this->prophet->prophesize('Blablacar\Test\Client');
        $client->set(
            Argument::type('string'),
            Argument::type('string')
        )->will(function ($args) {
            $this->get($args[0])->willReturn($args[1])->shouldBeCalledTimes(1);

            return true;
        })->shouldBeCalledTimes(1);

        $handler = new SessionHandler($client->reveal());

        $this->assertTrue($handler->write('key', 'value'));
        $this->assertEquals('value', $handler->read('key'));
    }
}
