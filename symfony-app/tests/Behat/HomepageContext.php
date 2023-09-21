<?php

namespace App\Tests\Behat;

use App\Infrastructure\RabbitMq\InMemoryProducer;
use App\Infrastructure\SSE\InMemoryPublisher;
use App\Job\Domain\InMemoryJobProvider;
use App\Job\Domain\Job;
use App\Job\Domain\JobId;
use Behat\Behat\Context\Context;
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
    public function iNavigateTo(string $path): void
    {
        $this->response = $this->client->request('GET', $path);
        if (200 !== $this->client->getResponse()->getStatusCode()) {
            throw new \RuntimeException(sprintf(
                'Expected response code 200, but got %d. Response body: "%s"',
                $this->client->getResponse()->getStatusCode(),
                $this->client->getResponse()->getContent())
            );
        }
    }

    /**
     * @Then /^the response should contains button "([^"]*)"$/
     */
    public function theResponseShouldContainsButton(string $txt): void
    {
        $count = $this->response->filterXPath("//button[text()='{$txt}']")->count();

        if ($count !== 1) {
            throw new \RuntimeException('Button not found');
        }
    }

    /**
     * @Then /^the response should contains link "([^"]*)" to download extension$/
     */
    public function theResponseShouldContainsLink(string $link): void
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
    public function iSubmitFormWithUrl(string $txt, string $url): void
    {
        $this->response = $this->client->submitForm($txt, ['url' => $url]);
    }

    /**
     * @When /^I submit form "([^"]*)"$/
     */
    public function iSubmitForm(string $txt): void
    {
        $this->response = $this->client->submitForm($txt);
    }

    /**
     * @Then /^I will be redirected to external page "([^"]*)"$/
     */
    public function iWillBeRedirectedToExternalPage(string $expectedLocation): void
    {
        if ($this->client->getResponse()->getStatusCode() !== 302) {
            throw new \RuntimeException(sprintf(
                'Expected 302 as response code, but got %d',
                $this->client->getResponse()->getStatusCode())
            );
        }

        $location = $this->client->getResponse()->headers->get('Location');

        if ($location !== $expectedLocation) {
            throw new \RuntimeException(sprintf('The actual location is "%s" not "%s"', $location, $expectedLocation));
        }
    }

    /**
     * @Given /^I will see flash message "(.*?)"$/
     */
    public function iWillSeeFlashMessage(string $message): void
    {
        if (1 !== $this->response->filter('.alert')->count()) {
            throw new \RuntimeException('Flash message element not found');
        }

        if (!str_contains($this->response->filter('.alert')->text(), $message)) {
            throw new \RuntimeException(sprintf('Flash message with content "%s" not found', $message));
        }
    }

    /**
     * @Given /^I follow redirects/
     */
    public function iFollowRedirection(): void
    {
        $this->client->followRedirects(true);
    }

    /**
     * @Then /^the url field should contain value "([^"]*)"$/
     */
    public function theUrlFieldShouldContainValue(string $url): void
    {
        $field = $this->response->filter("input#urlInput");

        if ($field->count() !== 1) {
            throw new \RuntimeException('Field not found');
        }

        if ($url !== $actualValue = $field->attr('value')) {
            throw new \RuntimeException(sprintf('The url field should have a value "%s" but have "%s"', $url, $actualValue));
        }
    }

    /**
     * @Then /^I will be waiting for processing "([^"]*)"$/
     */
    public function iWillBeWaitingForProcessing(string $url): void
    {
        if ($this->client->getResponse()->getStatusCode() !== 302) {
            throw new \RuntimeException(sprintf(
                    'Expected 302 as response code, but got %d',
                    $this->client->getResponse()->getStatusCode())
            );
        }

        $location = $this->client->getResponse()->headers->get('Location');

        if (!str_starts_with($location, '/redirect/')) {
            throw new \RuntimeException(sprintf('The actual location is "%s" not "%s"', $location, '/redirecct/<job-id>'));
        }

        /** @var InMemoryProducer $inMemory */
        $inMemory = $this->client->getKernel()->getContainer()->get(InMemoryProducer::class);

        if (1 !== $inMemory->count()) {
            throw new \RuntimeException('The message has not been published to create readable version of webpage');
        }

        if (false === str_contains($inMemory->getData(0)[0], json_encode($url))) {
            throw new \RuntimeException(sprintf(
                'The published message does not contain expected URL ("%s") to process',
                $url
            ));
        }

        /** @var InMemoryPublisher $inMemory */
        $inMemoryPublisher = $this->client->getKernel()->getContainer()->get(InMemoryPublisher::class);
        if (1 !== $inMemoryPublisher->count()) {
            throw new \RuntimeException('The SSE event has not been published');
        }
    }

    /**
     * @Given /^The job "([^"]*)" has been processed$/
     */
    public function theJobHasBeenProcessed(string $jobId): void
    {
        $job = Job::newJob(new JobId($jobId), 'https://example.com');
        $job->setPushToKindleUrl('https://fake-push-to-kindle.com/' . $jobId);

        /** @var InMemoryJobProvider $inMemoryProvider */
        $inMemoryProvider = $this->client->getKernel()->getContainer()->get(InMemoryJobProvider::class);
        $inMemoryProvider->appendJob($job);
    }

    /**
     * @When /^I open "([^"]*)"$/
     */
    public function iOpen(string $path): void
    {
        $this->client->request('GET', $path);
    }
}
