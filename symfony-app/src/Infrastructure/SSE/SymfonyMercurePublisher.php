<?php

namespace App\Infrastructure\SSE;

use App\Domain\Contract\SSEPublisherInterface;
use App\Domain\Dto\SSE\BaseEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class SymfonyMercurePublisher implements SSEPublisherInterface
{
    public function __construct(private readonly HubInterface $hub)
    {
    }

    public function publish(BaseEvent $data): void
    {
        $this->hub->publish(
            new Update(sprintf('job://%s', $data->getJobId()), json_encode($data)),
        );
    }
}
