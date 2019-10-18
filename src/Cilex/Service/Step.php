<?php
namespace Cilex\Service;
use Cilex\Service\DateTime;
Class Step
{
    public $start_date;
    public $end_date;
    public $duration;
    public $commit;

    public function start(DateTime $date_time = null) {
        if (!$date_time):
            $date_time = new DateTime();
        endif;
        $this->start_date = $date_time->getTime();
        return $this;
    }

    public function stop(DateTime $date_time = null) {
        if (!$date_time):
            $date_time = new DateTime();
        endif;
        if (!$this->end_date):
            $this->end_date = $date_time->getTime();
            $this->setDuration();
        endif;
        return $this;
    }

    public function setDuration() {
        $start_date = (array) $this->start_date;
        $start_date = new DateTime($start_date['date']);
        $start_date = $start_date->getTime();
        
        $end_date = (array) $this->end_date;
        if (!$end_date):
            $end_date = new DateTime();
        else: 
            $end_date = new DateTime($end_date['date']);
        endif;
        $end_date = $end_date->getTime();
        
        $diff = $start_date->diff($end_date);
        $this->duration = $diff->format('%H:%I:%S');
        return $this;
    }

    public function getDuration(): ?string {
        $this->setDuration();
        return $this->duration;
    }
}
