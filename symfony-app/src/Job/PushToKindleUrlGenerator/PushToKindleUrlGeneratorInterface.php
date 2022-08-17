<?php

namespace App\Job\PushToKindleUrlGenerator;

use App\Job\Domain\Job;

interface PushToKindleUrlGeneratorInterface
{
    public function createUrl(Job $job): string;
}
