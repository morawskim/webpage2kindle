<?php

namespace App\Controller;

use App\Dto\WebExtensionRequest;
use App\Service\SynchronousPushToKindleFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class WebExtensionController extends AbstractController
{
    #[Route('/web-extension', name: 'web_extension', methods: ['POST'])]
    public function process(
        WebExtensionRequest $webExtensionRequest,
        SynchronousPushToKindleFacade $pushToKindleFacade,
        ConstraintViolationListInterface $errors
    ): JsonResponse {
        if ($errors->count()) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $pushToKindleUrl = $pushToKindleFacade->processWebPageContent(
            $webExtensionRequest->url,
            $webExtensionRequest->body,
            $webExtensionRequest->title,
        );

        return new JsonResponse(['pushToKindleUrl' => $pushToKindleUrl]);
    }
}
