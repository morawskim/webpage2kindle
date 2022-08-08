<?php

namespace App\Controller;

use App\Service\SynchronousPushToKindleFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route("/", name: 'homepage', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('homepage.html.twig');
    }

    #[Route("/synchronous", name: 'push_to_kindle_synchronous', methods: ['POST'])]
    public function sync(Request $request, SynchronousPushToKindleFacade $pushToKindleFacade): Response
    {
        $url = $request->request->get('url');
        $pushToKindleUrl = $pushToKindleFacade->run($url);

        return new RedirectResponse($pushToKindleUrl);
    }
}
