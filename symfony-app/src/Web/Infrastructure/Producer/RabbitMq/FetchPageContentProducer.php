<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Job\Domain\JobId;
use App\Web\Application\Message\FetchPageContentMessage;
use App\Web\Domain\Contract\FetchPageContentProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class FetchPageContentProducer implements FetchPageContentProducerInterface
{
    public function __construct(
        #[Autowire(service: 'old_sound_rabbit_mq.fetch_page_content_producer')]
        private readonly Producer $producer,
        private readonly SerializerInterface $encrypterSerializer,
    ) {
    }

    public function publishFetchPageContent(JobId $jobId): void
    {
        $this->producer->publish($this->encrypterSerializer->serialize(new FetchPageContentMessage($jobId), 'json'));
    }
}
