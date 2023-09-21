<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

/**
 * @phpstan-type EventAsArray array{type: string, data: array<string, mixed>}
 */
abstract class BaseEvent implements \JsonSerializable
{
    abstract public function getJobId(): JobId;
}
