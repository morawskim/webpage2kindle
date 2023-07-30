<?php

namespace App\Infrastructure\Prometheus;

use Prometheus\Storage\Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisAdapterFactory
{
    public static function fromSymfonyRedisDSN(string $dsn): Redis
    {
        return Redis::fromExistingConnection(
            RedisAdapter::createConnection($dsn)
        );
    }
}
