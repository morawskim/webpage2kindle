<?php

namespace App\Domain\Contract;

use App\Domain\Dto\SSE\BaseEvent;

interface SSEPublisherInterface
{
    public function publish(BaseEvent $data): void;
}
