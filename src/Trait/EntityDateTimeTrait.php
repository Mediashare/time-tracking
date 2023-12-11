<?php

namespace Mediashare\TimeTracking\Trait;

use Mediashare\TimeTracking\Entity\Commit;
use Mediashare\TimeTracking\Entity\Step;
use Mediashare\TimeTracking\Entity\Tracking;

trait EntityDateTimeTrait {
    public function getStartDate(): int|null {
        switch (self::class) {
            case Tracking::class:
                $startDate =
                    $this->getCommits()?->first()?->getStartDate()
                    ?? $this->getSteps()?->first()?->getStartDate()
                ;
                break;
            case Commit::class:
                $startDate = $this->getSteps()?->first()?->getStartDate();
                break;
            case Step::class:
                $startDate = $this->startDate;
                break;
        }

        return $startDate;
    }

    public function getStartDateFormated(string $format): string|null {
        return $this->getStartDate()
            ? (new \DateTime())->setTimestamp($this->getStartDate())->format($format)
            : null
        ;
    }

    public function getEndDate(): int|null {
        switch (self::class) {
            case Tracking::class:
                $endDate =
                    $this->getCommits()?->first()?->getEndDate()
                    ?? $this->getSteps()?->first()?->getEndDate()
                ;
                break;
            case Commit::class:
                $endDate = $this->getSteps()?->first()?->getEndDate();
                break;
            case Step::class:
                $endDate = $this->endDate;
                break;
        }

        return $endDate;
    }

    public function getEndDateFormated(string $format): string|null {
        return $this->getEndDate()
            ? (new \DateTime())->setTimestamp($this->getEndDate())->format($format)
            : null
        ;
    }
}