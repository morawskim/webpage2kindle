<?php

namespace App\Infrastructure\RabbitMq;

use PhpAmqpLib\Wire\IO\StreamIO;
use Psr\Log\LoggerInterface;

class DebugStreamIO extends StreamIO
{
    private ?LoggerInterface $logger = null;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function checkBrokerHeartbeat(): void
    {
        $now = microtime(true);
        $lastActivity = $this->getLastActivity();

        if (null === $this->logger) {
            parent::checkBrokerHeartbeat();
            return;
        }

        $this->logger->debug('check heartbeat');

        if (($now - $lastActivity) > $this->heartbeat * 2 + 1) {
            $this->logger->error(
                sprintf(
                    'Exception will be throw, because now is "%s", lastActivity is "%s", heartbeat is "%s"',
                    $now,
                    $lastActivity,
                    $this->heartbeat,
                )
            );
        }

        parent::checkBrokerHeartbeat();
    }
}
