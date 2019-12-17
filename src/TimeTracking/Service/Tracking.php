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
        $last_step = end($this->steps);
        if (!$last_step->commit):
            $last_step->commit = $commit->id;
            $last_step->stop();
            if ($this->run):
                // Start new Step
                $step = new Step();
                $this->steps[] = $step->start();
            endif;
            
            // Commit
            $commit->step = $last_step; // Update Commit
            $this->commits[] = $commit; // Record Commit
        else:
            // Already commited...
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
        return $this->duration['hours'] . ':' . $this->duration['minutes'] . ':' . $this->duration['seconds'];
    }

    public function setDuration(bool $total = false) {
        // Init
        $duration = [
            'hours' => '00',
            'seconds' => '00',
            'minutes' => '00'
        ];

        if (!$total): // Calcul with current Step
            // Stop last step for current duration
            $step = end($this->steps);
            if (!$step->duration):
                $step->stop();
            endif;
        endif;

        // Steps incrementation
        foreach ($this->steps as $step):
            $step_duration = $step->duration;
            if ($step_duration):
                $parser = explode(':', $step_duration);
                $duration['hours'] += $parser[0];
                $duration['minutes'] += $parser[1];
                $duration['seconds'] += $parser[2];
            endif;
        endforeach;
        


        // Convert seconds to minutes
        if ($duration['seconds'] >= 60):
            $duration['minutes'] += (int) number_format($duration['seconds'] / 60);
            $duration['seconds'] = (int) number_format($duration['seconds'] % 60);
        endif;
        // Convert minutes to hours
        if ($duration['minutes'] >= 60):
            $duration['hours'] += (int) number_format($duration['minutes'] / 60);
            $duration['minutes'] = (int) number_format($duration['minutes'] % 60);
        endif;

        // Format 00:00:00
        if (strlen($duration['hours']) === 1): $duration['hours'] = '0' . $duration['hours']; endif;
        if (strlen($duration['minutes']) === 1): $duration['minutes'] = '0' . $duration['minutes']; endif;
        if (strlen($duration['seconds']) === 1): $duration['seconds'] = '0' . $duration['seconds']; endif;
        
        // Record
        $this->duration = $duration;
        return $this;
    }
}