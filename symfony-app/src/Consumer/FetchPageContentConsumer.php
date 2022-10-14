<?php

namespace App\Consumer;

use App\Job\Domain\JobId;
use App\Job\PushToKindlePipelineService;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Message\AMQPMessage;

class FetchPageContentConsumer
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly Producer $producer,
    ) {
    }

    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body, true);

        $this->pushToKindlePipeline->fetchPageContentForJob(new JobId($body['jobId']));
        $this->producer->publish(json_encode(['jobId' => $body['jobId']]));
    }
}
