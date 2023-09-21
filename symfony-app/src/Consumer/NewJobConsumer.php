<?php

namespace App\Consumer;

use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\NewJobMessage;
use App\Web\Domain\Contract\FetchPageContentProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class NewJobConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly FetchPageContentProducerInterface $fetchPageContentProducer,
        private readonly SerializerInterface $encrypterSerializer,
    ) {
    }

    public function execute(AMQPMessage $msg)
    {
        $dto = $this->encrypterSerializer->deserialize($msg->body, NewJobMessage::class, 'json');

        $this->pushToKindlePipeline->addNewJob($dto->jobId, $dto->url);
        $this->fetchPageContentProducer->publishFetchPageContent($dto->jobId);

        return ConsumerInterface::MSG_ACK;
    }
}
