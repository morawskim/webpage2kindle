<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Job\Domain\JobId;
use App\Web\Application\Message\CreatePushToKindleUrlMessage;
use App\Web\Domain\Contract\PushToKindleProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class PushToKindleProducer implements PushToKindleProducerInterface
{
    public function __construct(
        #[Autowire(service: 'old_sound_rabbit_mq.push_to_kindle_url_producer')]
        private readonly Producer $producer,
        private readonly SerializerInterface $encrypterSerializer,
    ) {
    }

    public function publishPushToKindle(JobId $jobId): void
    {
        $this->producer->publish($this->encrypterSerializer->serialize(new CreatePushToKindleUrlMessage($jobId), 'json'));
    }
}
