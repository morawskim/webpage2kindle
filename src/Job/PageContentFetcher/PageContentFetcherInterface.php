<?php

namespace App\Job\PageContentFetcher;

interface PageContentFetcherInterface
{
    public function getPageContent(string $url): string;
}
