<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Web\Domain\Contract\NewJobProducerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class NewJobProducer implements NewJobProducerInterface
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        #[Autowire(service: 'old_sound_rabbit_mq.new_job_producer')]
        private readonly Producer $producer,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function publishNewJob(string $url): JobId
    {
        $jobId = $this->jobRepository->createNextJobId();
        $dto = ['jobId' => (string) $jobId, 'url' => $url];

        $this->producer->publish($this->serializer->serialize($dto, 'json'));

        return $jobId;
    }
}
