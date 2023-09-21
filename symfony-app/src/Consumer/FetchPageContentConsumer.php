<?php

namespace App\Consumer;

use App\Job\Exception\CannotGetPageContentException;
use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\FetchPageContentMessage;
use App\Web\Domain\Contract\PushToKindleProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class FetchPageContentConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly PushToKindleProducerInterface $pushToKindleProducer,
        private readonly SerializerInterface $encrypterSerializer,
    ) {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            /** @var FetchPageContentMessage $dto */
            $dto = $this->encrypterSerializer->deserialize($msg->body, FetchPageContentMessage::class, 'json');

            $this->pushToKindlePipeline->fetchPageContentForJob($dto->jobId);
            $this->pushToKindleProducer->publishPushToKindle($dto->jobId);
        } catch (CannotGetPageContentException $e) {
        }

        return ConsumerInterface::MSG_ACK;
    }
}
