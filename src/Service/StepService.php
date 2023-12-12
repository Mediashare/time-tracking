<?php
namespace Mediashare\Marathon\Service;

use Mediashare\Marathon\Entity\Step;

class StepService {
    public function createStep(
        string|null $startDate = null,
        string|null $endDate = null,
    ): Step {
        return (new Step())
            ->setStartDate($startDate ?? (new \DateTime())->getTimestamp())
            ->setEndDate($endDate)
        ;
    }

    /**
     * Create step with custom duration
     *
     * @param string $duration (exemple: '+5minutes', '+2hours', '+1days')
     * @param int|null $startDate Timestamp of startDate
     */
    public function createStepWithCustomDuration(string $duration, int|null $startDate = null): Step {
        $startDate = $startDate ?? (new \DateTime())->getTimestamp();
        $endDate = strtotime($duration, $startDate);

        return (new Step())
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }
}