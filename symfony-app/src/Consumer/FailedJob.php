<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class FailedJob implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        // todo mmo analyse job?
        return ConsumerInterface::MSG_ACK;
    }
}
