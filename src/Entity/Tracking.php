<?php
namespace Mediashare\Entity;

use DateTime as GlobalDateTime;
use Mediashare\Service\DateTime;

Class Tracking {
    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $duration;
    public $commits = [];
    public $steps = [];
    public $run = false;
    public $status;

    public function getStatus() {
        if (!$this->status && $this->run):
                $this->status = 'run';
        elseif (!$this->status):
            $this->status = 'pause';
        endif;
        
        return $this->status;
    }

    public function getCurrentStep() {
        foreach (array_reverse($this->steps) ?? [] as $step):
            if (!$step->commit):
                return $step;
            endif;
        endforeach;
        return null;
    }

    public function getStartDate(): string {
        if (is_array($this->start_date)):
            $start_date = new DateTime($this->start_date['date']);
            $start_date = $start_date->getTime();
        else: 
            $start_date = $this->start_date;
        endif;

        return $start_date->format('d/m/Y H:i:s');
    }

    public function getEndDate(): ?string {
        if (is_array($this->end_date)):
            $end_date = new DateTime($this->end_date['date']);
            $end_date = $end_date->getTime();
        elseif ($this->end_date instanceof GlobalDateTime): 
            $end_date = $this->end_date;
        else: 
            return null;
        endif;

        return $end_date->format('d/m/Y H:i:s');
    }
    
    public function getDuration(bool $total = false): string {
        $this->setDuration($total);
        return $this->duration;
    }

    public function setDuration(bool $total = false) {
        // Init
        $seconds = 0;

        if (!$total): // Calcul with current Step
            // Stop last step for current duration
            $step = end($this->steps);
            if ($step->duration === "00:00:00"):
                $step->setEndDate();
            endif;
        endif;
        
        // Steps incrementation
        $datetime = new DateTime();
        foreach ($this->commits ?? [] as $index => $commit):
            foreach ($commit->steps ?? [] as $step):
                $datetime->addStep($step);
            endforeach;
        endforeach;
        // Record
        $this->duration = $datetime->getDuration();
        
        return $this;
    }

    public function getCommits() {
        $commits = $this->commits ?? [];
        // Order by date
        usort($commits, function($a, $b) {
            $ad = \strtotime($a->getEndDate());
            $bd = \strtotime($b->getEndDate());
            if ($ad == $bd): return 0; endif;
            return $ad < $bd ? -1 : 1;
        });

        $datetime = new DateTime();
        foreach ($commits ?? [] as $index => $commit) {
            foreach ($commit->steps ?? [] as $step):
                $datetime->addStep($step);
            endforeach;
            // Record
            $results[] = [
                'index' => $index + 1,
                'id' => $commit->id,
                'message' => $commit->message,
                'duration' => $commit->getDuration(),
                'duration_total' => $datetime->getDuration(),
                'startDate' => $commit->getStartDate(),
                'endDate' => $commit->getEndDate(),
            ];
        }


        return $results ?? [];
    }

    public function getInformations() {
        // Current Step
        $current_step = new DateTime();
        foreach (array_reverse($this->steps) as $step):
            if (!$step->commit):
                $current_step->addStep($step);
            endif;
        endforeach;

        $informations = [
            'id' => $this->id,
            'name' => $this->name,
            'running' => \ucfirst($this->getStatus()),
            'commits' => (string) count($this->commits),
            'duration' => $this->getDuration(),
            'current_timer' => $current_step->getDuration(),
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate()
        ];

        return $informations;
    }
}
