<?php

namespace App\Job\PageContentFetcher;

use App\Job\Exception\CannotGetPageContentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GoReadabilitySynchronousPageContentFetcher implements PageContentFetcherInterface
{
    public function __construct(private readonly string $pathToCommand)
    {
    }

    public function getPageContent(string $url): string
    {
        $process = new Process([$this->pathToCommand, $url]);
        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $errorOutput = $e->getProcess()->getErrorOutput();

            if (str_contains($errorOutput, 'the page is not readable')) {
                throw CannotGetPageContentException::default('The page is not readable', $e);
            }

            throw $e;
        }

        return $process->getOutput();
    }
}
