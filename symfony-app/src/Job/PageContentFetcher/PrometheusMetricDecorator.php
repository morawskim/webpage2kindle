<?php

namespace App\Job\PageContentFetcher;

use App\Job\PrometheusHelper;

class PrometheusMetricDecorator implements PageContentFetcherInterface
{
    public function __construct(
        private readonly PageContentFetcherInterface $pageContentFetcher,
        private readonly PrometheusHelper $prometheusHelper,
        private readonly string $prometheusAdapterLabel,
    ) {
    }

    public function getPageContent(string $url): string
    {
        $content = $this->pageContentFetcher->getPageContent($url);
        $this->prometheusHelper->publishProcessedArticleMetrics($this->prometheusAdapterLabel, strlen($content));

        return $content;
    }
}
