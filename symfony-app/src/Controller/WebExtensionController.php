<?php

namespace App\Controller;

use App\Dto\WebExtensionRequest;
use App\Job\PrometheusHelper;
use App\Service\SynchronousPushToKindleFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class WebExtensionController extends AbstractController
{
    #[Route('/web-extension', name: 'web_extension', methods: ['POST'])]
    public function process(
        #[MapRequestPayload()] WebExtensionRequest $webExtensionRequest,
        SynchronousPushToKindleFacade $pushToKindleFacade,
        Request $request,
        PrometheusHelper $prometheusHelper
    ): JsonResponse {
        $prometheusHelper->webExtensionVersionMetric($request->headers->get('X-Extension-Version', 'unknown'));

        $pushToKindleUrl = $pushToKindleFacade->processWebPageContent(
            $webExtensionRequest->url,
            $webExtensionRequest->body,
            $webExtensionRequest->title,
        );

        return new JsonResponse(['pushToKindleUrl' => $pushToKindleUrl]);
    }
}
