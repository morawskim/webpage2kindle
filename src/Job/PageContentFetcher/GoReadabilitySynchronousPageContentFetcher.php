<?php

namespace App\Job\PageContentFetcher;

use Symfony\Component\Process\Process;

class GoReadabilitySynchronousPageContentFetcher implements PageContentFetcherInterface
{
    public function getPageContent(string $url): string
    {
        $process = new Process([__DIR__ . '/../../../bin/go-readability', $url]);
        $process->mustRun();

        return $process->getOutput();
    }
}
