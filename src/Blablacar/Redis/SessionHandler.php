<?php

namespace Blablacar\Redis;

class SessionHandler implements \SessionHandlerInterface
{
    protected $client;
    protected $prefix;
    protected $ttl;

    public function __construct(Client $client, $prefix = 'session', $ttl = null)
    {
        $this->client = $client;
        $this->prefix = $prefix;
        $this->ttl    = $ttl;
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
        return 0 < $this->client->del($this->getSessionKey($sessionId));
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        return true;
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
}
