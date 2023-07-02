<?php
namespace Mediashare\Service;

use Mediashare\Entity\Step;

class DateTime {
    public $date_time;
    public $steps = [];
    public $seconds = 0;
    public $duration = '00:00:00';

    public function __construct(string $date_time = 'now') {
        $date = new \DateTime($date_time);
        // $date->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->date_time = $date;
    }

    /**
     * Get DateTime Interface
     *
     * @return \DateTime
     */
    public function getTime() {
        return $this->date_time;
    }

    /**
     * Add new step
     *
     * @param Step $step
     * @return self
     */
    public function addStep($step) {
        if (is_array($step)):
            $stepEntity = new Step();
            $stepEntity->start_date = $step['start_date'] ?? $this->getTime();
            $stepEntity->end_date = $step['end_date'];
            $stepEntity->seconds = $step['seconds'] ?? 0;
            $stepEntity->getDuration();
            $stepEntity->commit = $step['commit'];
            $step = $stepEntity;
        endif;

        $this->steps[] = $step;
        return $this;
    }

    /**
     * Convert seconds to HH:ii:ss
     *
     * @return string
     */
    public function getDuration() {
        $seconds = $this->getSeconds();
        $this->duration = sprintf('%02d:%02d:%02d', ($seconds/3600),($seconds/60%60), $seconds%60);
        return $this->duration;
    }

    /**
     * Get total seconds from step(s)
     *
     * @return int
     */
    public function getSeconds() {
        $seconds = 0;
        foreach ($this->steps ?? [] as $step):
            $parser = explode(':', $step->getDuration());
            $seconds += (($parser[0] ?? 0) * 60 * 60) + (($parser[1] ?? 0) * 60) + $parser[2] ?? 0;
        endforeach;
        $this->seconds = $seconds;
        return $this->seconds;
    }
}
