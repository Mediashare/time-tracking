<?php
namespace Mediashare\Service;

use Mediashare\Service\Report;
use Mediashare\Service\Session;
use Mediashare\Entity\Tracking as TrackingEntity;

Class Tracking {
    private $session;
    public function __construct(?string $id = null) {
        $this->session = new Session();
    }

    public function init(?string $id = null) {
        if ($id):
            return $this->get($id);
        else:
            return $this->getLast();
        endif;
    }

    public function create(?string $id = null, ?string $name = null) {
        $tracking = new TrackingEntity();
        $tracking->id = $id ?? \uniqid();
        $tracking->name = $name;
        return $tracking;
    }

    public function getLast() {
        $tracking = new TrackingEntity();
        // Get last session
        if ($this->session->info):
            $tracking->id = $this->session->info->id;
            $report = new Report($tracking);
            $tracking = $report->read($report->file);
            if (!empty($tracking)):    
                return $tracking;
            endif; 
        endif;
        
        return false; // Session not found
    }

    public function get(string $id) {
        $tracking = new TrackingEntity();
        $tracking->id = $id; 
        $report = new Report($tracking);
        $tracking = $report->read($report->file);

        if (!empty($tracking)):
            // Remove old session
            if ($this->session->info):
                if ($this->session->info->id !== $id):
                    $this->session->remove();
                endif;
            endif;
            // Rewrite new session
            $this->session->create($tracking);

            return $tracking;
        endif;

        return false; // Session not found
    }

    public function all() {
        $trackings = [];
        foreach (glob('./.time-tracking/report-*') as $report):
            $tracking_id = str_replace('report-', '', \basename($report));
            $tracking_id = str_replace('.json', '', $tracking_id);
            
            $tracking = new TrackingEntity();
            $tracking->id = $tracking_id;
            
            $report = new Report($tracking);
            $tracking = $report->read($report->file);
            // Informations
            $trackings[] = $tracking->getInformations();
        endforeach;

        // Order by date
        usort($trackings, function($a, $b) {
            $ad = new \DateTime(strtotime($a['date']));
            $bd = new \DateTime(strtotime($b['date']));
            if ($ad == $bd): return 0; endif;
            return $ad < $bd ? -1 : 1;
        });
        
        return $trackings;
    }
}