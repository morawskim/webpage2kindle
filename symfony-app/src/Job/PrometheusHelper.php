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

    /**
     * @param array<string, mixed> $data
     */
    public function publishRequestMetrics(array $data, float $duration, int $memoryPeakBytes): void
    {
        $histogram = $this->collectorRegistry->getOrRegisterHistogram(
            $this->metricNamespace,
            'execution_time',
            'Execution time (ms)',
            array_keys($data),
            [50, 150, 250, 400, 500, 700, 900, 1000, 1200, 1500, 2500, 4000]
        );
        $histogram->observe($duration, array_values($data));

        $histogram = $this->collectorRegistry->getOrRegisterHistogram(
            $this->metricNamespace,
            'memory_usage',
            'Memory usage (MiB)',
            array_keys($data),
            [10, 25, 40, 50, 75, 90, 128, 150, 180, 200, 256]
        );
        $histogram->observe((int)($memoryPeakBytes / 1024 / 1024), array_values($data));
    }

    public function webExtensionVersionMetric(string $webExtensionVersion): void
    {
        $this->collectorRegistry->getOrRegisterCounter(
            $this->metricNamespace,
            'web_extension_version',
            'Total',
            ['version']
        )->inc([$webExtensionVersion]);
    }

    public function publishMessageProcessingTime(array $data, float $duration): void
    {
        $histogram = $this->collectorRegistry->getOrRegisterHistogram(
            $this->metricNamespace,
            'message_processing_time',
            'Processing time (ms)',
            array_keys($data),
            [50, 150, 250, 400, 500, 700, 900, 1000, 1200, 1500, 2500, 4000]
        );

        $histogram->observe($duration, array_values($data));
    }
}
