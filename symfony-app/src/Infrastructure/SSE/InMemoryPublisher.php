<?php

namespace App\Infrastructure\SSE;

use App\Domain\Contract\SSEPublisherInterface;
use App\Domain\Dto\SSE\BaseEvent;

class InMemoryPublisher implements SSEPublisherInterface, \Countable
{
    /** @var array<mixed> */
    private array $data = [];

    public function publish(BaseEvent $data): void
    {
        $this->data[] = $data;
    }

    public function count(): int
    {
        return count($this->data);
    }
}
