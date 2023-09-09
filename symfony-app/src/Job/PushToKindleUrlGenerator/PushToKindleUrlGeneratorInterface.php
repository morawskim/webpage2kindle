<?php

namespace App\Job\PushToKindleUrlGenerator;

use App\Job\Domain\Job;
use App\Job\Exception\CannotCreatePastePadUrlException;

interface PushToKindleUrlGeneratorInterface
{
    /**
     * @throws CannotCreatePastePadUrlException
     */
    public function createUrl(Job $job): string;
}
