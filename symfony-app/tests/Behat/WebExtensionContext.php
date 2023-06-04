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
            ['Content-Type' => 'application/x-www-form-urlencoded', 'HTTP_ACCEPT' => 'application/json'],
        );
    }

    /**
     * @Then I get a response with status ":httpResponseCode" and body:
     */
    public function iGetAResponseWithStatusAndBody(int $httpResponseCode, PyStringNode $expectedResponseBody): void
    {
        if ($httpResponseCode !== $actualHttpResponseCode = $this->client->getResponse()->getStatusCode()) {
            throw new \RuntimeException(sprintf('Expected HTTP status code "%d" got "%d"', $httpResponseCode, $actualHttpResponseCode));
        }


        if ($this->prettifyJson($this->client->getResponse()->getContent()) !== $this->prettifyJson($expectedResponseBody->getRaw())) {
            throw new \RuntimeException(sprintf(
                'Response body does not match. Expected "%s" got "%s"',
                $expectedResponseBody->getRaw(),
                $this->client->getResponse()->getContent()
            ));
        }
    }

    private function prettifyJson(string $json): string
    {
        $decodedJson = json_decode($json, false, flags: JSON_THROW_ON_ERROR);

        return json_encode($decodedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
