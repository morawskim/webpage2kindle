<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

class HomepageContext implements Context
{
    private Crawler $response;

    public function __construct(private KernelBrowser $client)
    {
    }

    /**
     * @Given /^I navigate to "([^"]*)"$/
     */
    public function iNavigateTo($path): void
    {
        $this->response = $this->client->request('GET', $path);
        if (200 !== $this->client->getResponse()->getStatusCode()) {
            throw new \RuntimeException('Response code is unexpected');
        }
    }

    /**
     * @Then /^the response should contains button "([^"]*)"$/
     */
    public function theResponseShouldContainsButton($txt): void
    {
        $count = $this->response->filterXPath("//button[text()='{$txt}']")->count();

        if ($count !== 1) {
            throw new \RuntimeException('Button not found');
        }
    }

    /**
     * @Then /^the response should contains link "([^"]*)"$/
     */
    public function theResponseShouldContainsLink($link): void
    {
        if (1 !== $this->response->selectLink($link)->count()) {
            throw new \RuntimeException('Link not found');
        }
    }

    /**
     * @When /^I submit form "([^"]*)" with url "([^"]*)"$/
     */
    public function iSubmitFormWithUrl($txt, $url): void
    {
        $this->client->submitForm($txt, ['url' => $url]);
    }

    /**
     * @Then /^I will be redirected to external page$/
     */
    public function iWillBeRedirectedToExternalPage(): void
    {
        if ($this->client->getResponse()->getStatusCode() !== 302) {
            throw new \RuntimeException('Response code is unexpected');
        }
    }
}
