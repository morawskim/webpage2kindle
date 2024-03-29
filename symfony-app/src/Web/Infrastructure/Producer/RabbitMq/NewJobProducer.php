<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Domain\Contract\SSEPublisherInterface;
use App\Domain\Dto\SSE\NewJob;
use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Web\Application\Message\NewJobMessage;
use App\Web\Domain\Contract\NewJobProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class NewJobProducer implements NewJobProducerInterface
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        #[Autowire(service: 'old_sound_rabbit_mq.new_job_producer')]
        private readonly ProducerInterface $producer,
        private readonly SerializerInterface $encrypterSerializer,
        private readonly SSEPublisherInterface $SSEPublisher,
    ) {
    }

    public function publishNewJob(string $url): JobId
    {
        $jobId = $this->jobRepository->createNextJobId();
        $dto = new NewJobMessage($jobId, $url);

        $this->producer->publish(
            $this->encrypterSerializer->serialize($dto, 'json'),
        );
        $this->SSEPublisher->publish(new NewJob($jobId));

        return $jobId;
    }
}
