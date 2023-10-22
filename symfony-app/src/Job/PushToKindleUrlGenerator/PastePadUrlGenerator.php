<?php

namespace App\Job\PushToKindleUrlGenerator;

use App\Job\Domain\Job;
use App\Job\Exception\CannotCreatePastePadUrlException;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
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
        try {
            $response = $this->pushToKindleClient->request('POST', '/post.php', $options->toArray());
            if (302 === $response->getStatusCode()) {
                $responseUrl = $response->getHeaders(false);
                return $responseUrl['location'][0];
            }
        } catch (TimeoutExceptionInterface $e) {
            throw CannotCreatePastePadUrlException::timeout();
        }

        throw CannotCreatePastePadUrlException::wrongResponseCode($response->getStatusCode());
    }
}
