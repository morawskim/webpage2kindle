<?php

namespace App\Job\PageContentFetcher;

use App\Job\Exception\CannotGetPageContentException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoReadabilityApiPageContentFetcher implements PageContentFetcherInterface
{
    public function __construct(private readonly HttpClientInterface $goReadabilityMicroserviceClient)
    {
    }

    public function getPageContent(string $url): string
    {
        $options = (new HttpOptions())
            ->setQuery(['url' => $url])
            ->toArray();
        try {
            $body = $this->goReadabilityMicroserviceClient->request('GET', '/', $options)->toArray();

            return '<h1>' . $body['title'] . '</h1>' . $body['content'];
        } catch (ServerExceptionInterface $e) {
            throw CannotGetPageContentException::default('The page is not readable', $e);
        } catch (ClientException $e) {
            if (400 === $e->getCode()) {
                $msg = $e->getResponse()->getContent(false);
                throw CannotGetPageContentException::default($msg, $e);
            }
        }
    }
}
