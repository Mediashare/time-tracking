<?php
namespace Mediashare\Service;

use Mediashare\Entity\Step;
use Mediashare\Entity\Tracking;
use Mediashare\Service\DateTime;
use Mediashare\Entity\Commit as CommitEntity;

Class Commit {
    public $tracking;
    public function __construct(Tracking $tracking) {
        $this->tracking = $tracking;
    }

    public function get(string $id) {
        foreach ($this->tracking->commits ?? [] as $index => $commit):
            if ($commit->id === $id):
                unset($this->tracking->commits[$index]);
                return $commit;
                break;
            endif;
        endforeach;
        return null;
    }

    public function create(?string $message = null, ?string $duration = null) {
        $commit = new CommitEntity();
        $commit->message = $message;
        if ($duration):
            $commit->addStep($this->customStep($commit, $duration));
            // Stop last Step
            foreach (array_reverse($this->tracking->steps) as $index => $step):
                if ($commit->duration && !$step->commit):
                    $step->commit = 'canceled';
                endif;
            endforeach;
            
        else:
            foreach (array_reverse($this->tracking->steps ?? []) as $step):
                if (!$step->commit):
                    $step->commit = $commit->id;
                    if (!$step->end_date):
                        $step->setEndDate();
                    endif;
                    // Steps
                    $commit->addStep($step); // Update Commit
                endif;
            endforeach;
        endif;
        $commit->getDuration();
        return $commit;
    }

    public function edit(string $id, ?string $message = null, ?string $duration = null) {
        $commit = $this->get($id);
        $commit->message = $message ?? $commit->message;
        if ($duration):
            $commit->steps = [];
            $commit->addStep($this->customStep($commit, $duration));
        endif;

        return $commit;
    }

    private function customStep(CommitEntity $commit, string $duration) {
        $step = new Step();
        $now = new DateTime('now');
        $step->setStartDate(new DateTime(
            date("Y-m-d H:i:s", $now->getTime()->getTimestamp()))
        );
        $step->setEndDate(new DateTime(
            date("Y-m-d H:i:s", strtotime($duration, $now->getTime()->getTimestamp()))
        ));

        return $step;
    }
}