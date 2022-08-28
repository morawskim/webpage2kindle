<?php

namespace App\Job\PushToKindleUrlGenerator;

use App\Job\Domain\Job;

class FakePushToKindleUrlGenerator implements PushToKindleUrlGeneratorInterface
{
    public function createUrl(Job $job): string
    {
        return 'https://fake-push-to-kindle.com';
    }
}
