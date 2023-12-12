<?php

namespace Mediashare\Marathon\Trait;

use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Entity\Timer;

trait EntityDurationTrait {
    /**
     * Convert seconds to "d H:i:s" format
     */
    public function getDuration(bool|null $onlyNotCommited = false, int $totalSeconds = 0): string {
        $seconds = $this->getSeconds($onlyNotCommited) + $totalSeconds;
        return $this->duration = trim(
            sprintf(
                '%s %02d:%02d:%02d',
                ((($seconds/86400%60) !== 0) ? ($seconds/86400%60) . 'd' : ''),
                ($seconds/3600%24),
                ($seconds/60%60),
                $seconds%60
            )
        );
    }

    public function getSeconds(bool|null $onlyNotCommited = false): int {
        switch (self::class) {
            case Timer::class:
                $seconds = array_sum(
                    array_merge(
                        !$onlyNotCommited
                            ? $this
                                ->getCommits()
                                ->map(
                                    fn (Commit $commit) => $commit->getSeconds()
                                )->toArray()
                            : [],
                        $this
                            ->getSteps()
                            ->map(fn (Step $step) => $step->getSeconds())
                            ->toArray(),
                    )
                );
                break;
            case Commit::class:
                $seconds = array_sum(
                    $this
                        ->getSteps()
                        ->map(fn (Step $step) => $step->getSeconds())
                        ->toArray()
                );
                break;
            case Step::class:
                $seconds = ($this->getEndDate() ?? (new \DateTime())->getTimestamp()) - $this->getStartDate();
                break;
        }

        return $seconds;
    }
}