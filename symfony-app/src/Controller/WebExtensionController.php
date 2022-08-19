<?php

namespace App\Controller;

use App\Service\SynchronousPushToKindleFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebExtensionController extends AbstractController
{
    #[Route('/web-extension', name: 'web_extension', methods: ['POST'])]
    public function process(Request $request, SynchronousPushToKindleFacade $pushToKindleFacade): JsonResponse
    {
        $url = $request->request->get('url');
        $body = $request->request->get('body');
        $jobId = $pushToKindleFacade->processWebPageContent($url, $body);

        return new JsonResponse(['jobId' => $jobId]);
    }
}
