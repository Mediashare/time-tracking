<?php
namespace Mediashare\Entity;

use Mediashare\Entity\Step;
use Mediashare\Service\DateTime;

Class Commit {
    public $id;
    public $start_date;
    public $end_date;
    public $message;
    public $steps = [];
    public $duration = '00:00:00';

    public function __construct(string $message = null) {
        $this->id = uniqid();
        $date = new DateTime();
        $this->end_date = $date->getTime();
        if ($message):
            $this->message = $message;
        endif;
    }

    public function getDuration(): string {
        $datetime = new DateTime();
        foreach ($this->steps ?? [] as $step):
            $datetime->addStep($step);
        endforeach;
        $this->duration = $datetime->getDuration();
        
        return $this->duration;
    }
    
    public function addStep(Step $step): self {
        $this->steps[] = $step;
        $this->getDuration();
        return $this;
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

    public function getEndDate(): string {
        if (is_array($this->end_date)):
            $end_date = new DateTime($this->end_date['date']);
            $end_date = $end_date->getTime();
        else: 
            $end_date = $this->end_date;
        endif;

        return $end_date->format('d/m/Y H:i:s');
    }
}
