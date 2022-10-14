<?php

namespace App\Consumer;

use App\Job\Domain\JobId;
use App\Job\PushToKindlePipelineService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Message\AMQPMessage;

class NewJobConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly Producer $producer,
    ) {
    }

    public function execute(AMQPMessage $msg)
    {
        $body = json_decode($msg->body, true);

        $this->pushToKindlePipeline->addNewJob(new JobId($body['jobId']), $body['url']);
        $this->producer->publish(json_encode(['jobId' => $body['jobId']]));
    }
}
