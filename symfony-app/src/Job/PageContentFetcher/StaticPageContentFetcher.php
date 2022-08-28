<?php

namespace App\Job\PageContentFetcher;

use App\Job\Exception\CannotGetPageContentException;

class StaticPageContentFetcher implements PageContentFetcherInterface
{
    public function getPageContent(string $url): string
    {
        return 'lorem ipsum ...';
    }
}
