<?php

namespace App\Infrastructure\RabbitMq;

use OldSound\RabbitMqBundle\RabbitMq\AMQPConnectionFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

#[AsDecorator(decorates: 'old_sound_rabbit_mq.connection_factory.default')]
class ConnectionFactory extends AMQPConnectionFactory
{
    public function __construct(
        private LoggerInterface $logger,
        #[AutowireDecorated]
        private AMQPConnectionFactory $inner,
    ) {
    }

    public function createConnection()
    {
        $connection =  $this->inner->createConnection();

        if ($connection instanceof DebugAMQPStreamConnection) {
            $connection->setLogger($this->logger);
        }

        return $connection;
    }
}
