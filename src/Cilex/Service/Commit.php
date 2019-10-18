<?php
namespace Cilex\Service;
use Cilex\Service\DateTime;
Class Commit
{
    public $id;
    public $create_date;
    public $message;
    public $step;

    public function __construct(string $message = null) {
        $this->id = uniqid();
        $date = new DateTime();
        $this->create_date = $date->getTime();
        if ($message):
            $this->message = $message;
        endif;
    }

    public function getDuration(): string {
        if (is_array($this->step)):
            return (string) $this->step['duration'];
        else:
            return (string) $this->step->duration;
        endif;
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
