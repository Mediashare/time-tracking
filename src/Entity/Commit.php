<?php
namespace Mediashare\Entity;

use Mediashare\Entity\Step;
use Mediashare\Service\DateTime;

Class Commit {
    public $id;
    public $create_date;
    public $message;
    public $steps;
    public $duration = '00:00:00';

    public function __construct(string $message = null) {
        $this->id = uniqid();
        $date = new DateTime();
        $this->create_date = $date->getTime();
        if ($message):
            $this->message = $message;
        endif;
    }

    public function getDuration(): string {
        $datetime = new DateTime();
        
        // Old version adaptation
        if (!empty($this->step)):
            if (is_array($this->step)):
                $step = new Step();
                $step->start_date = $this->step['start_date'];
                $step->end_date = $this->step['end_date'];
                $this->step = $step;
            endif;
            $datetime->addStep($this->step);
        endif;

        foreach ($this->steps ?? [] as $step):
            $datetime->addStep($step);
        endforeach;
        $this->duration = $datetime->getDuration();
        
        return $this->duration;
    }

    public function getCreateDate(): string {
        if (is_array($this->create_date)):
            $create_date = new DateTime($this->create_date['date']);
            $create_date = $create_date->getTime();
        else: 
            $create_date = $this->create_date;
        endif;

        return $create_date->format('d/m/Y H:i:s');
    }
}
