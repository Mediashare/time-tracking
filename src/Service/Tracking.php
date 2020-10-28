<?php
namespace Mediashare\Service;
use Mediashare\Service\DateTime;
use Mediashare\Service\Report;
use Mediashare\Service\Session;
use Mediashare\Service\Commit;
use Mediashare\Service\Step;
Class Tracking
{
    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $duration;
    public $commits = [];
    public $steps = [];
    public $run = false;
    public $report;

    public function start() {
        // Init Tracking
        if (!$this->id):$this->id = uniqid();endif; // Set $id
        $date = new DateTime();
        $start_date = $date->getTime(); // Get current date time
        if (!$this->start_date):
            $this->start_date = $start_date; // Set $start_date
        endif;
        // Step
        $last_step = end($this->steps); // Get last Step
        if (!$last_step || $last_step->end_date):
            $step = new Step();
            $this->steps[] = $step->start($date); // Start step
        endif;

        if (!$this->report):$this->report = new Report($this);endif; // Report file
        
        $this->run = true;
        return $this;
    }
    
    public function stop() {
        $this->run = false;
        if (!$this->end_date):
            $date = new DateTime();
            $end_date = $date->getTime();
            $this->end_date = $end_date;
        endif;

        // Stop last step
        $last_step = end($this->steps);
        $last_step->stop();
        
        return $this;
    }

    public function commit(Commit $commit) {
        // Stop last Step
        foreach (array_reverse($this->steps) as $step):
            if (!$step->commit):
                $step->commit = $commit->id;
                $step->stop();
                
                // Steps
                $commit->steps[] = $step; // Update Commit
            endif;
        endforeach;
        
        // Record Commit
        $this->commits[] = $commit;
        
        if ($this->run):
            // Start new Step
            $step = new Step();
            $this->steps[] = $step->start();
        endif;
        
        return $this; 
    }

    public function getStatus() {
        if ($this->run):
            return "Run";
        else:
            return "Pause";
        endif;
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
                $step->stop();
            endif;
        endif;
        
        // Steps incrementation
        foreach ($this->steps as $step):
            if ($step->commit):
                $step_duration = $step->duration;
                if ($step_duration):
                    $parser = explode(':', $step_duration);
                    $seconds += (($parser[0] ?? 0) * 60 * 60) + (($parser[1] ?? 0) * 60) + $parser[2] ?? 0;
                endif;
            endif;
        endforeach;

        // Convert seconds to HH:ii:ss
        $duration = sprintf('%02d:%02d:%02d', ($seconds/3600),($seconds/60%60), $seconds%60);

        // Record
        $this->duration = $duration;
        return $this;
    }
}
