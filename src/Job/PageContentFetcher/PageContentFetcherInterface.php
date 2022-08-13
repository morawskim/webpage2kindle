<?php

namespace App\Job\PageContentFetcher;

use App\Job\Exception\CannotGetPageContentException;

interface PageContentFetcherInterface
{
    /**
     * @throws CannotGetPageContentException
     */
    public function getPageContent(string $url): string;
}
