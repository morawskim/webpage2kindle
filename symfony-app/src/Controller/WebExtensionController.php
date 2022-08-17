<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebExtensionController extends AbstractController
{
    #[Route('/web-extension', name: 'web_extension', methods: ['POST'])]
    public function process(Request $request): JsonResponse
    {
        $body = $request->request->get('body');

        return new JsonResponse(['ok' => $body]);
    }
}
