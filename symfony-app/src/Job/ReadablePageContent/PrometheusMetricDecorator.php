<?php

namespace App\Job\ReadablePageContent;

use App\Job\PrometheusHelper;

class PrometheusMetricDecorator implements CreateReadablePageContentInterface
{
    public function __construct(
        private readonly CreateReadablePageContentInterface $createReadablePageContent,
        private readonly PrometheusHelper $prometheusHelper,
        private readonly string $prometheusAdapterLabel,
    ) {
    }

    public function createReadableVersionOfWebPageContent(string $body, string $url): string
    {
        $content = $this->createReadablePageContent->createReadableVersionOfWebPageContent($body, $url);
        $this->prometheusHelper->publishProcessedArticleMetrics($this->prometheusAdapterLabel, strlen($content));

        return $content;
    }
}
