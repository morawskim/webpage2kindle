<?php

namespace App\Infrastructure\Symfony\Event;

use PhpAmqpLib\Connection\AbstractConnection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CloseRabbitMqConnectionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'old_sound_rabbit_mq.connection.default', lazy: true)]
        private readonly AbstractConnection $connection,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => ['closeRabbitMqConnection']
        ];
    }

    public function closeRabbitMqConnection(): void
    {
        if ('frankenphp' === php_sapi_name()) {
            $this->connection->close();
        }
    }
}
