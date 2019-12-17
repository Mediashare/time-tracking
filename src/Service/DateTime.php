<?php
namespace Mediashare\Service;
class DateTime
{
    public $date_time;
    
    public function __construct(string $date_time = null) {
        $date = new \DateTime($date_time);
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->date_time = $date;
    }

    public function getTime() {
        return $this->date_time;
    }
}
