<?php
namespace Mediashare\Service;
use Mediashare\Service\DateTime;
use Mediashare\Service\Duration;
Class Commit {
    public $id;
    public $create_date;
    public $message;
    public $steps = [];
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
        $duration = new Duration();
        foreach ($this->steps ?? [] as $step):
            $duration->addStep($step);
        endforeach;
        
        $this->duration = $duration->getDuration();
        
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
