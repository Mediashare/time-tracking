<?php
namespace Mediashare\Entity;

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

    public function getCreateDate(): string {
        if (is_array($this->start_date)):
            $create_date = new DateTime($this->start_date['date']);
            $create_date = $create_date->getTime();
        else: 
            $create_date = $this->start_date;
        endif;

        return $create_date->format('d/m/Y H:i:s');
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
            $ad = \strtotime($a->getCreateDate());
            $bd = \strtotime($b->getCreateDate());
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
                'date' => $commit->getCreateDate(),
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
            'date' => $this->getCreateDate()
        ];

        return $informations;
    }
}
