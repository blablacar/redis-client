<?php

namespace Blablacar\Redis;

use Blablacar\Redis\Exception\LockException;

class SessionHandler implements \SessionHandlerInterface
{
    protected $client;
    protected $prefix;
    protected $ttl;
    protected $spinLockWait;
    protected $lockMaxWait;

    /**
     * __construct
     *
     * @param Client $client       A redis client
     * @param string $prefix       The prefix to use for keys (default: "session")
     * @param int    $ttl          A ttl for keys (default: null = no ttl)
     * @param int    $spinLockWait The time to wait in ms before lock try
     * @param int    $lockMaxWait  The maximum time to wait before exiting if no lock
     *
     * @return void
     */
    public function __construct(Client $client, $prefix = 'session', $ttl = null, $spinLockWait = 150000, $lockMaxWait = 30000000)
    {
        $this->client       = $client;
        $this->prefix       = $prefix;
        $this->ttl          = $ttl;
        $this->spinLockWait = $spinLockWait;
        $this->lockMaxWait  = $lockMaxWait;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        $key = $this->getSessionKey($sessionId);

        if (false === $key = $this->client->get($key)) {
            return null;
        }

        return $key;
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $this->lock($sessionId);

        if (null === $this->ttl) {
            return $this->client->set(
                $this->getSessionKey($sessionId),
                (string) $data
            );
        }

        return $this->client->setex(
            $this->getSessionKey($sessionId),
            $this->ttl,
            (string) $data
        );
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        $this->client->del($this->getSessionKey($sessionId));
        $this->unlock($sessionId);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * lock
     *
     * @param string $sessionId
     *
     * @return boolean
     */
    protected function lock($sessionId)
    {
        $attempts = $this->lockMaxWait / $this->spinLockWait;
        $lockKey = $this->getSessionLockKey($sessionId);
        for ($i = 0; $i < $attempts; $i++) {
            if ($this->client->setnx($lockKey, null)) {
                $this->client->expire($lockKey, $this->lockMaxWait + 1);

                return true;
            }
            usleep($attempts);
        }

        throw new LockException();
    }

    /**
     * unlock
     *
     * @param string $sessionId
     *
     * @return void
     */
    protected function unlock($sessionId)
    {
        $this->client->del($this->getSessionLockKey($sessionId));
    }

    /**
     * getSessionKey
     *
     * @param string $sessionId
     *
     * @return string
     */
    private function getSessionKey($sessionId)
    {
        return sprintf('%s:%s', $this->prefix, $sessionId);
    }

    /**
     * getSessionLockKey
     *
     * @param string $sessionId
     *
     * @return string
     */
    protected function getSessionLockKey($sessionId)
    {
        return sprintf('%s:%s.lock', $this->prefix, $sessionId);
    }
}
