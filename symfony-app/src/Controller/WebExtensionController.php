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
        // todo mmo validation
        $url = $request->request->get('url');
        $body = $request->request->get('body');
        $title = $request->request->get('title');
        $pushToKindleUrl = $pushToKindleFacade->processWebPageContent($url, $body, $title);

        return new JsonResponse(['pushToKindleUrl' => $pushToKindleUrl]);
    }
}
