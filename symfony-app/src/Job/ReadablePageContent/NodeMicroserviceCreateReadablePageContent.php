<?php

namespace App\Job\ReadablePageContent;

use App\Job\Exception\CannotCreateReadableVersionException;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NodeMicroserviceCreateReadablePageContent implements CreateReadablePageContentInterface
{
    public function __construct(private readonly HttpClientInterface $nodeMicroserviceClient)
    {
    }

    public function createReadableVersionOfWebPageContent(string $body, string $url, string $title): string
    {
        $options = (new HttpOptions())
            ->setBody(['body' => $body, 'url' => $url, 'title' => $title])
            ->toArray();
        try {
            $body = $this->nodeMicroserviceClient->request('POST', '/process-webpage', $options)->toArray();

            return $body['body'];
        } catch (ServerExceptionInterface $e) {
            throw CannotCreateReadableVersionException::default($e);
        }
    }
}
