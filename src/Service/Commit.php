<?php
namespace Mediashare\Service;
use Mediashare\Service\DateTime;
Class Commit
{
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
        $seconds = 0;
        foreach ($this->steps as $step):
            if (is_array($step)):
                $duration = (string) $step['duration'];
            else:
                $duration = (string) $step->duration;
            endif;
            $parser = explode(':', $duration);
            $seconds += (($parser[0] ?? 0) * 60 * 60) + (($parser[1] ?? 0) * 60) + $parser[2] ?? 0;
        endforeach;

        $this->duration = sprintf('%02d:%02d:%02d', ($seconds/3600),($seconds/60%60), $seconds%60);

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
