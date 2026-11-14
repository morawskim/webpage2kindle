<?php

namespace App\Infrastructure\Messenger;

use App\Job\PrometheusHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceivedStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class MessageProcessingTimeSubscriber implements EventSubscriberInterface
{
    private ?int $startTime = null;

    public function __construct(private readonly PrometheusHelper $prometheusHelper)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => ['beforeProcessingMessage'],
            WorkerMessageHandledEvent::class => ['afterProcessingMessage'],
            WorkerMessageFailedEvent::class => ['afterProcessingMessage'],
        ];
    }

    public function beforeProcessingMessage(WorkerMessageReceivedEvent $event): void
    {
        $this->startTime = hrtime(true);
    }

    public function afterProcessingMessage(WorkerMessageHandledEvent|WorkerMessageFailedEvent $event): void
    {
        if (null === $this->startTime) {
            return;
        }

        $startTime = $this->startTime;
        $this->startTime = null;
        $duration = ceil((hrtime(true) - $startTime) / 1e+6);
        [$exchange, $routingKey] = $this->getExchangeAndRoutingKey($event->getEnvelope());

        $this->prometheusHelper->publishMessageProcessingTime([
            'exchange' => $exchange,
            'routingKey' => $routingKey,
        ], $duration);
    }

    /**
     * @return array{string, string}
     */
    private function getExchangeAndRoutingKey(Envelope $envelope): array
    {
        $amqpStamp = $envelope->last(AmqpReceivedStamp::class);
        if ($amqpStamp instanceof AmqpReceivedStamp) {
            return [
                (string) $amqpStamp->getAmqpEnvelope()->getExchangeName(),
                (string) $amqpStamp->getAmqpEnvelope()->getRoutingKey(),
            ];
        }

        // the transports are named after the routing keys
        return ['', $envelope->last(ReceivedStamp::class)?->getTransportName() ?? ''];
    }
}
