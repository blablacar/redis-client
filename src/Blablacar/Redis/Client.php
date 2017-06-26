<?php

namespace Blablacar\Redis;

class Client
{
    protected $host;
    protected $port;
    protected $base;

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * __construct
     *
     * @param string $host
     * @param int    $port
     * @param int    $base
     *
     * @return void
     */
    public function __construct($host = '127.0.0.1', $port = 6379, $base = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->base = $base;
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

        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port);
        if (null !== $this->base) {
            $this->redis->select($this->base);
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

    /**
     * Get the value related to the specified key.
     *
     * @param string $key
     *
     * @return bool|string
     */
    public function get($key)
    {
        if (null === $this->redis) {
            $this->connect();
        }

        return $this->redis->get($key);
    }

    /**
     * Set the string value in argument as value of the key, with a time to live.
     *
     * @param   string  $key
     * @param   int     $ttl
     * @param   string  $value
     *
     * @return  bool    TRUE if the command is successful.
     */
    public function setex($key, $ttl, $value)
    {
        if (null === $this->redis) {
            $this->connect();
        }

        return $this->redis->setex($key, $ttl, $value);
    }
}