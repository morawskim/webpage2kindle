<?php

namespace App\Job\PageContentFetcher;

use Symfony\Component\Process\Process;

class GoReadabilitySynchronousPageContentFetcher implements PageContentFetcherInterface
{
    public function __construct(private readonly string $pathToCommand)
    {
    }

    public function getPageContent(string $url): string
    {
        $process = new Process([$this->pathToCommand, $url]);
        $process->mustRun();

        return $process->getOutput();
    }
}
