<?php

namespace App\Command;

use App\Domain\Contract\SSEPublisherInterface;
use App\Domain\Dto\SSE\BaseEvent;
use App\Job\Domain\JobId;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestSseCommand extends Command
{
    public function __construct(private readonly SSEPublisherInterface $SSEPublisher)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('test:sse-publisher')
            ->setDescription('Test publishing SSE message')
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->SSEPublisher->publish(new class extends BaseEvent
        {
            private JobId $jobId;

            public function __construct()
            {
                $this->jobId = new JobId("test-" . hrtime(true));
            }

            public function getJobId(): JobId
            {
                return $this->jobId;
            }

            public function jsonSerialize(): array
            {
                return [
                    'type' => 'test_job',
                    'data' => [
                        'jobId' => (string) $this->jobId,
                    ]
                ];
            }
        });

        return Command::SUCCESS;
    }
}
