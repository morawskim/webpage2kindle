<?php

namespace App\Infrastructure\RabbitMq;

use App\Job\PrometheusHelper;
use OldSound\RabbitMqBundle\Event\AfterProcessingMessageEvent;
use OldSound\RabbitMqBundle\Event\BeforeProcessingMessageEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageProcessingTimeSubscriber implements EventSubscriberInterface
{
    /** @var array<int, array{string, string, int}> */
    private array $map = [];

    public function __construct(private readonly PrometheusHelper $prometheusHelper)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeProcessingMessageEvent::NAME => ['beforeProcessingMessage'],
            AfterProcessingMessageEvent::NAME => ['afterProcessingMessage'],
        ];
    }

    public function beforeProcessingMessage(BeforeProcessingMessageEvent $event): void
    {
        $tag = $event->getAMQPMessage()->getDeliveryTag();
        $exchange = $event->getAMQPMessage()->getExchange();
        $routingKey = $event->getAMQPMessage()->getRoutingKey();

        $this->map[$tag] = [$exchange, $routingKey, hrtime(true)];
    }

    public function afterProcessingMessage(AfterProcessingMessageEvent $event): void
    {
        $tag = $event->getAMQPMessage()->getDeliveryTag();

        if (!isset($this->map[$tag])) {
            return;
        }

        [$exchange, $routingKey, $startTime] = $this->map[$tag];
        unset($this->map[$tag]);
        $duration = ceil((hrtime(true) - $startTime) / 1e+6);
        $data = [
            'exchange' => $exchange,
            'routingKey' => $routingKey,
        ];

        $this->prometheusHelper->publishMessageProcessingTime($data, $duration);
    }
}
