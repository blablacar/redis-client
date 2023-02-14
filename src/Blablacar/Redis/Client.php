<?php

namespace Blablacar\Redis;

class Client
{
    protected $host;
    protected $port;
    protected $timeout;
    protected $base;
    protected $password;

    protected $redis;

    /**
     * __construct
     *
     * By default the timeout value is 0 meaning it will use default_socket_timeout.
     *
     * @param string $host
     * @param int    $port
     * @param float  $timeout
     * @param int    $base
     * @param string $password
     *
     * @return void
     */
    public function __construct($host = '127.0.0.1', $port = 6379, $timeout = 0.0, $base = null, $password = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->base = $base;
        $this->password = $password;
    }

    /**
     * __destruct
     *
     * @return void
     */
    public function __destruct()
    {
        if (null === $this->redis) {
            return;
        }

        $this->redis->close();
    }

    /**
     * connect
     *
     * @return void
     */
    public function connect()
    {
        if (null !== $this->redis) {
            return;
        }

        $context = null;
        if ($this->password !== null) {
            $context = ['auth' => ['pass' => $this->password]];
        }

        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port, $this->timeout, null, 0, 0, $context);
        if (null !== $this->base) {
            try {
                $this->redis->select($this->base);
            } catch (\RedisException $e) {
                throw new \RedisException(sprintf('%s (%s:%d)', $e->getMessage(), $this->host, $this->port), $e->getCode(), $e);
            }
        }
    }

    /**
     * open
     *
     * @return void
     */
    public function open()
    {
        $this->connect();
    }

    /**
     * close
     *
     * @return void
     */
    public function close()
    {
        if (null === $this->redis) {
            return;
        }

        $this->redis->close();
        $this->redis = null;
    }

    /**
     * getRedis
     *
     * @return \Redis|null
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * __call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (null === $this->redis) {
            $this->connect();
        }

        return call_user_func_array(array($this->redis, $name), $arguments);
    }

    /**
     * @param string      $key
     * @param int         $iterator
     * @param string|null $pattern
     * @param int         $count
     *
     * @return mixed
     */
    public function hscan($key, &$iterator, $pattern = null, $count = 0)
    {
        if (null === $this->redis) {
            $this->connect();
        }

        return $this->redis->hscan($key, $iterator, $pattern, $count);
    }
}
