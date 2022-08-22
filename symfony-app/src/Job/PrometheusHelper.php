<?php

namespace App\Job;

use Prometheus\CollectorRegistry;

class PrometheusHelper
{
    public function __construct(
        private readonly CollectorRegistry $collectorRegistry,
        private readonly string $metricNamespace = 'app',
    ) {
    }

    public function publishProcessedArticleMetrics(string $adapter, int $bytes): void
    {
        $this->collectorRegistry->getOrRegisterCounter(
            $this->metricNamespace,
            'page_content_fetcher_counter',
            'Total number of request to fetch pages',
            ['adapter']
        )->inc([$adapter]);

        $this->collectorRegistry->getOrRegisterCounter(
            $this->metricNamespace,
            'page_content_fetcher_total_bytes',
            'Total number of fetched bytes',
            ['adapter']
        )->incBy($bytes, [$adapter]);
    }
}
