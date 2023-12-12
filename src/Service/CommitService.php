<?php
namespace Mediashare\Marathon\Service;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Timer;
use Mediashare\Marathon\Exception\CommitNotFoundException;

class CommitService {
    private Timer $timer;
    private StepService $stepService;

    public function __construct(
        private Config $config,
    ) {
        $timerService = new TimerService($this->config);
        $this->timer = $timerService->getTimer();
        $this->stepService = new StepService();
    }

    public function createCommit(?string $message = null, ?string $duration = null): Timer {
        $commit = (new Commit())
            ->setId((new \DateTime())->format('YmdHis'))
            ->setMessage($message);

        if ($duration):
            $commit->addStep(
                $this->stepService->createStepWithCustomDuration(
                    $duration,
                    ($lastStep = $this->timer->getSteps()?->last())?->getEndDate()
                        ? $lastStep->getStartDate()
                        : null
                )
            );
        else:
            /** @var Step $step */
            foreach ($this->timer->getSteps() as $step):
                if (!$step->getEndDate()):
                    $step->setEndDate((new \DateTime())->getTimestamp());
                endif;
                $commit->addStep($step);
            endforeach;
        endif;

        $this->timer
            ->addCommit($commit)
            ->getSteps()->clear();

        if ($this->timer->isRun()):
            $this
                ->timer
                ->addStep(
                    $this->stepService->createStep()
                );
        endif;

        return $this->timer;
    }

    /**
     * @throws CommitNotFoundException
     */
    public function editCommit(
        string $id,
        string|false $message = false,
        string|false $duration = false,
    ): Timer {
        if (($commit = $this
                ->timer
                ->getCommits()
                ->findOneBy(
                    fn (Commit $commit) => $commit->getId() === $id
                )) === null
        ) {
            throw new CommitNotFoundException();
        }

        $key = $this->timer->getCommits()->getKey($commit);

        if ($message !== false):
            $this
                ->timer
                ->getCommits()
                ->offsetSet($key, $commit->setMessage($message));
        endif;

        if ($duration !== false):
            $startDate = $commit->getStartDate() ?? (new \DateTime())->getTimestamp();
            $commit->getSteps()->clear();
            $this
                ->timer
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

        return $this->timer;
    }

    /**
     * @throws CommitNotFoundException
     */
    public function removeCommit(
        string $id,
    ): Timer {
        if (($commit = $this->timer->getCommits()->findOneBy(fn(Commit $commit) => $commit->getId() === $id)) === null):
            throw new CommitNotFoundException();
        endif;

        $this->timer->getCommits()->remove($commit);

        return $this->timer;
    }
}