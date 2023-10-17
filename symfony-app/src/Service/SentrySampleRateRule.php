<?php

namespace App\Service;

use Sentry\Tracing\SamplingContext;

class SentrySampleRateRule
{
    public function __invoke(SamplingContext $context): float
    {
        if ($context->getParentSampled()) {
            // If the parent transaction (for example a JavaScript front-end)
            // is sampled, also sample the current transaction
            return 1.0;
        }

        if ('prometheus_metrics' === ($context->getTransactionContext()?->getData()['route'] ?? '')) {
            return 0.1;
        }

        // Default sample rate for all other transactions (replaces `traces_sample_rate`)
        return 1.0;
    }
}
