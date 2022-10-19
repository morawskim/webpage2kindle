<?php

namespace App\Consumer;

use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\CreatePushToKindleUrlMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class CreatePushToKindleUrlConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function execute(AMQPMessage $msg): void
    {
        /** @var CreatePushToKindleUrlMessage $dto */
        $dto = $this->serializer->deserialize($msg->body, CreatePushToKindleUrlMessage::class, 'json');

        $this->pushToKindlePipeline->createPushToKindleUrl($dto->jobId);
    }
}
