<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class HomepageContext implements Context
{
    private Crawler $response;

    public function __construct(private KernelBrowser $client, private KernelInterface $kernel)
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
     * @Then /^the response should contains link "([^"]*)" to download extension$/
     */
    public function theResponseShouldContainsLink($link): void
    {
        if (1 !== $this->response->selectLink($link)->count()) {
            throw new \RuntimeException('Link not found');
        }

        $href = $this->response->selectLink($link)->attr('href');
        $filePath = $this->kernel->getProjectDir() . '/public' . $href;

        if (!file_exists($filePath)) {
            throw new \RuntimeException('Extension not exists');
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
