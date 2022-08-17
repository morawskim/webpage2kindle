<?php

namespace App\Job\PageContentFetcher;

use Prometheus\CollectorRegistry;

class PrometheusMetricDecorator implements PageContentFetcherInterface
{
    public function __construct(
        private readonly PageContentFetcherInterface $pageContentFetcher,
        private readonly CollectorRegistry $collectorRegistry,
        private readonly string $prometheusAdapterLabel,
        private readonly string $metricNamespace = 'app',
    ) {
    }

    public function getPageContent(string $url): string
    {
        $content = $this->pageContentFetcher->getPageContent($url);

        $this->collectorRegistry->getOrRegisterCounter(
            $this->metricNamespace,
            'page_content_fetcher_counter',
            'Total number of request to fetch pages',
            ['adapter']
        )->inc([$this->prometheusAdapterLabel]);

        $this->collectorRegistry->getOrRegisterCounter(
            $this->metricNamespace,
            'page_content_fetcher_total_bytes',
            'Total number of fetched bytes',
            ['adapter']
        )->incBy(strlen($content), [$this->prometheusAdapterLabel]);

        return $content;
    }
}
