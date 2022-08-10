<?php

namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricController
{
    #[Route("/metrics", name: 'prometheus_metrics', methods: ['GET'])]
    public function metrics(CollectorRegistry $collectorRegistry, RenderTextFormat $renderTextFormat): Response
    {
        $result = $renderTextFormat->render($collectorRegistry->getMetricFamilySamples());

        return new Response($result, headers: ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
