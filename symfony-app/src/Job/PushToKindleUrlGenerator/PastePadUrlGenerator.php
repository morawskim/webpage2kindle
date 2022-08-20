<?php

namespace App\Job\PushToKindleUrlGenerator;

use App\Job\Domain\Job;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PastePadUrlGenerator implements PushToKindleUrlGeneratorInterface
{
    public function __construct(private readonly HttpClientInterface $pushToKindleClient)
    {
    }

    public function createUrl(Job $job): string
    {
        $options = (new HttpOptions())
            ->setBody(['body' => $job->getWebPageContent(), 'action' => 'Push to Kindle']);

        $response = $this->pushToKindleClient->request('POST', '/post.php', $options->toArray());
        $responseUrl = $response->getHeaders(false);

        return $responseUrl['location'][0];
    }
}
