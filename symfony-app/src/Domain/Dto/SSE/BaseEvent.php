<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

abstract class BaseEvent implements \JsonSerializable
{
    abstract public function getJobId(): JobId;
}
