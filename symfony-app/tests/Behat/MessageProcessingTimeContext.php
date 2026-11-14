<?php

namespace App\Tests\Behat;

use App\Infrastructure\Messenger\MessageProcessingTimeSubscriber;
use Behat\Behat\Context\Context;
use Prometheus\Storage\Adapter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class MessageProcessingTimeContext implements Context
{
    public function __construct(
        #[Autowire(service: 'prometheus_storage')]
        private readonly Adapter $prometheusStorage,
        private readonly MessageProcessingTimeSubscriber $messageProcessingTimeSubscriber
    ) {
    }

    #[\Behat\Step\Given('/^The afterEvent has not been called$/')]
    public function theAfterEventHasNotBeenCalled(): void
    {
        $this->messageProcessingTimeSubscriber->beforeProcessingMessage(
            new WorkerMessageReceivedEvent($this->createEnvelope(), 'new_job')
        );
    }

    #[\Behat\Step\When('/^The another message has been processed$/')]
    public function theAnotherMessageHasBeenProcessed(): void
    {
        $envelope = $this->createEnvelope();

        $this->messageProcessingTimeSubscriber->beforeProcessingMessage(
            new WorkerMessageReceivedEvent($envelope, 'new_job')
        );

        $this->messageProcessingTimeSubscriber->afterProcessingMessage(
            new WorkerMessageHandledEvent($envelope, 'new_job')
        );
    }

    #[\Behat\Step\Then('/^We should store one metric$/')]
    public function weShouldStoreOneMetric(): void
    {
        $metrics = $this->prometheusStorage->collect();

        if (2 !== count($metrics)) {
            throw new \RuntimeException(sprintf('Expected two metrics in storage. Got "%d"', count($metrics)));
        }

        if ('app_message_processing_time' !== $metrics[1]->getName()) {
            throw new \RuntimeException(sprintf(
                'Expected the name of the second metric will be "app_message_processing_time". Got "%s"',
                $metrics[1]->getName()
            ));
        }
    }

    private function createEnvelope(): Envelope
    {
        return new Envelope(new \stdClass(), [new ReceivedStamp('new_job')]);
    }
}
