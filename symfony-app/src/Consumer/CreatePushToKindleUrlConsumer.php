<?php

namespace App\Consumer;

use App\Job\Domain\JobId;
use App\Job\PushToKindlePipelineService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class CreatePushToKindleUrlConsumer implements ConsumerInterface
{
    public function __construct(private readonly PushToKindlePipelineService $pushToKindlePipeline)
    {
    }

    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body, true);

        $this->pushToKindlePipeline->createPushToKindleUrl(new JobId($body['jobId']));
    }
}
