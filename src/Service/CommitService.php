<?php
namespace Mediashare\TimeTracking\Service;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Step;
use Mediashare\TimeTracking\Entity\Commit;
use Mediashare\TimeTracking\Entity\Tracking;
use Mediashare\TimeTracking\Exception\CommitNotFoundException;

class CommitService {
    private Tracking $tracking;
    private StepService $stepService;

    public function __construct(
        private Config $config,
    ) {
        $trackingService = new TrackingService($this->config);
        $this->tracking = $trackingService->getTracking();
        $this->stepService = new StepService();
    }

    public function createCommit(?string $message = null, ?string $duration = null): Tracking {
        $commit = (new Commit())
            ->setId((new \DateTime())->format('YmdHis'))
            ->setMessage($message);

        if ($duration):
            $commit->addStep(
                $this->stepService->createStepWithCustomDuration(
                    $duration,
                )
            );
        else:
            /** @var Step $step */
            foreach ($this->tracking->getSteps() as $step):
                if (!$step->getEndDate()):
                    $step->setEndDate((new \DateTime())->getTimestamp());
                endif;
                $commit->addStep($step);
            endforeach;
        endif;

        $this->tracking
            ->addCommit($commit)
            ->getSteps()->clear();

        if ($this->tracking->isRun()):
            $this
                ->tracking
                ->addStep(
                    $this->stepService->createStep()
                );
        endif;

        return $this->tracking;
    }

    public function editCommit(
        string $id,
        string|false $message = false,
        string|false $duration = false,
    ): Tracking {
        if (($commit = $this
                ->tracking
                ->getCommits()
                ->findOneBy(
                    fn (Commit $commit) => $commit->getId() === $id
                )) === null
        ) {
            throw new CommitNotFoundException();
        }

        $key = $this->tracking->getCommits()->getKey($commit);

        if ($message !== false):
            $this
                ->tracking
                ->getCommits()
                ->offsetSet($key, $commit->setMessage($message));
        endif;

        if ($duration !== false):
            $startDate = $commit->getStartDate() ?? (new \DateTime())->getTimestamp();
            $commit->getSteps()->clear();
            $this
                ->tracking
                ->getCommits()
                ->offsetSet(
                    $key,
                    $commit
                        ->addStep(
                            $this
                                ->stepService
                                ->createStepWithCustomDuration(
                                    $duration,
                                    $startDate,
                                )
                        )
                );
        endif;

        return $this->tracking;
    }

    public function removeCommit(
        string $id,
    ): Tracking {
        if (($commit = $this->tracking->getCommits()->findOneBy(fn(Commit $commit) => $commit->getId() === $id)) === null):
            throw new CommitNotFoundException();
        endif;

        $this->tracking->getCommits()->remove($commit);

        return $this->tracking;
    }
}