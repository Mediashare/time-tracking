<?php
namespace Mediashare\Entity;
use Mediashare\Service\DateTime;
Class Step {
    public $start_date;
    public $end_date;
    public $duration;
    public $commit;

    public function setStartDate(DateTime $date_time = null) {
        if (!$date_time):
            $date_time = new DateTime();
        endif;
        $this->start_date = $date_time->getTime();
        return $this;
    }

    public function setEndDate(DateTime $date_time = null) {
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
        if (empty($end_date)):
            $end_date = new DateTime();
        else: 
            $end_date = new DateTime($end_date['date']);
        endif;
        $end_date = $end_date->getTime();
        
        $diff = $start_date->diff($end_date);
        
        $hours = $diff->h;
        $hours += $diff->days*24;
        $duration = $hours.":".$diff->format('%I:%S');

        $this->duration = $duration;
        return $this;
    }

    public function getDuration(bool $end = true): ?string {
        $this->setDuration();
        $duration = $this->duration;
        if (!$end):
            $this->end_date = null;
            $this->duration = null;
        endif;
        return $duration;
    }
}
