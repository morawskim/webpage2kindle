<?php

namespace App\Controller;

use App\Job\Domain\GetNewestJobsInterface;
use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Job\Exception\CannotGetPageContentException;
use App\Service\AsyncPushToKindleFacade;
use App\Service\SynchronousPushToKindleFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route("/", name: 'homepage', methods: ['GET'])]
    public function home(Request $request): Response
    {
        return $this->render('homepage.html.twig', ['url' => urldecode($request->get('url', ''))]);
    }

    #[Route("/synchronous", name: 'push_to_kindle_synchronous', methods: ['POST'])]
    public function sync(Request $request, SynchronousPushToKindleFacade $pushToKindleFacade): Response
    {
        $url = $request->request->get('url');

        if (empty($url)) {
            $this->addFlash('error', 'URL cannot be empty');
            return $this->redirectToRoute('homepage');
        }

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addFlash('error', sprintf('URL "%s" is invalid', $url));
            return $this->redirectToRoute('homepage');
        }

        try {
            $pushToKindleUrl = $pushToKindleFacade->run($url);

            return new RedirectResponse($pushToKindleUrl);
        } catch (CannotGetPageContentException $e) {
            return $this->render('sync_job_error.html.twig', ['reason' => $e->getMessage(), 'url' => $url]);
        }
    }

    #[Route("/async-and-wait", name: 'push_to_kindle_async_and_wait', methods: ['POST'])]
    public function asyncAndWait(Request $request, AsyncPushToKindleFacade $asyncPushToKindleFacade)
    {
        $url = $request->request->get('url');

        if (empty($url)) {
            $this->addFlash('error', 'URL cannot be empty');
            return $this->redirectToRoute('homepage');
        }

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addFlash('error', sprintf('URL "%s" is invalid', $url));
            return $this->redirectToRoute('homepage');
        }

        $jobId = $asyncPushToKindleFacade->publishNewJob($url);
        sleep(1);

        return $this->redirectToRoute('job_details', ['jobId' => (string) $jobId]);
    }

    #[Route("/newest-jobs", name: 'list_newest_jobs', methods: ['GET'])]
    public function newestJobs(GetNewestJobsInterface $getNewestJobs): Response
    {
        $data = $getNewestJobs->getNewestJobs();

        return $this->render('newest.html.twig', ['data' => $data]);
    }

    #[Route('/details/{jobId}', name: 'job_details')]
    public function jobChangelog(string $jobId, JobRepository $jobRepository): Response
    {
        $records = $jobRepository->getJobDetailsAsStream(new JobId($jobId));

        return $this->render('job_details.html.twig', ['records' => $records]);
    }
}
