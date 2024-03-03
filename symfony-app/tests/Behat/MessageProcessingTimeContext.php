<?php

namespace App\Tests\Behat;

use App\Infrastructure\RabbitMq\MessageProcessingTimeSubscriber;
use Behat\Behat\Context\Context;
use OldSound\RabbitMqBundle\Event\AfterProcessingMessageEvent;
use OldSound\RabbitMqBundle\Event\BeforeProcessingMessageEvent;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Prometheus\Storage\Adapter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MessageProcessingTimeContext implements Context
{
    private Consumer $fakeConsumer;

    public function __construct(
        #[Autowire(service: 'prometheus_storage')]
        private readonly Adapter $prometheusStorage,
        private readonly MessageProcessingTimeSubscriber $messageProcessingTimeSubscriber
    ) {
        $this->fakeConsumer = new Consumer(new class extends AbstractConnection {
            public function __construct() {}

            public function connectOnConstruct(): bool
            {
                return false;
            }
        });
    }

    /**
     * @Given /^The afterEvent has not been called$/
     */
    public function theAfterEventHasNotBeenCalled(): void
    {
        $message = new AMQPMessage('', []);
        $message->setDeliveryInfo(1, false, 'exchange', 'routingKey');

        $this->messageProcessingTimeSubscriber->beforeProcessingMessage(
            new BeforeProcessingMessageEvent(
                $this->fakeConsumer,
                $message
            )
        );
    }

    /**
     * @When /^The another message has been processed$/
     */
    public function theAnotherMessageHasBeenProcessed(): void
    {
        $message = new AMQPMessage('', []);
        $message->setDeliveryInfo(2, false, 'exchange', 'routingKey');

        $this->messageProcessingTimeSubscriber->beforeProcessingMessage(
            new BeforeProcessingMessageEvent(
                $this->fakeConsumer,
                $message
            )
        );

        $this->messageProcessingTimeSubscriber->afterProcessingMessage(
            new AfterProcessingMessageEvent(
                $this->fakeConsumer,
                $message
            )
        );
    }

    /**
     * @Then /^We should store one metric$/
     */
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
}
