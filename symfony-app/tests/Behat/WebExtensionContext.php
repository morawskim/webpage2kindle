<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

class WebExtensionContext implements Context
{
    private Crawler $response;

    public function __construct(private KernelBrowser $client)
    {
    }

    /**
     * @Given I send POST request to :path with body:
     */
    public function iSendPOSTRequestToWithBody(string $path, PyStringNode $requestBody): void
    {
        $params = [];
        parse_str($requestBody, $params);

        $this->response = $this->client->request(
            'POST',
            $path,
            $params,
            [],
            ['Content-Type' => 'application/x-www-form-urlencoded'],
        );
        if (200 !== $this->client->getResponse()->getStatusCode()) {
            throw new \RuntimeException('Response code is unexpected');
        }
    }

    /**
     * @Then I get a response with status ":httpResponseCode" and body:
     */
    public function iGetAResponseWithStatusAndBody(int $httpResponseCode, PyStringNode $expectedResponseBody): void
    {
        if ($httpResponseCode !== $actualHttpResponseCode = $this->client->getResponse()->getStatusCode()) {
            throw new \RuntimeException(sprintf('Expected HTTP status code "%d" got "%d"', $httpResponseCode, $actualHttpResponseCode));
        }

        if ($this->client->getResponse()->getContent() !== $expectedResponseBody->getRaw()) {
            throw new \RuntimeException(sprintf(
                'Response body does not match. Expected "%s" got "%s"',
                $expectedResponseBody->getRaw(),
                $this->client->getResponse()->getContent()
            ));
        }
    }
}
