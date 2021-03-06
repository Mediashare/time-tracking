<?php
namespace Mediashare\Service;

use Mediashare\Entity\Step;
use Mediashare\Entity\Commit;
use Mediashare\Service\Output;
use Mediashare\Service\Report;
use Mediashare\Entity\Tracking;
use Mediashare\Service\Session;
use Mediashare\Service\DateTime;

Class Controller {
    public $tracking;
    private $session;
    public function __construct(Tracking $tracking) {
        $this->session = new Session();
        $this->tracking = $tracking;
    }

    /**
     * Start timer
     *
     * @return self
     */
    public function start() {
        // Init Tracking
        if (!$this->tracking->id):$this->tracking->id = uniqid();endif; // Set $id
        $date = new DateTime();
        $start_date = $date->getTime(); // Get current date time
        if (!$this->tracking->start_date):
            $this->tracking->start_date = $start_date; // Set $start_date
        endif;
        // Step
        $last_step = end($this->tracking->steps); // Get last Step
        if (!$last_step || $last_step->end_date):
            $step = new Step();
            $this->tracking->steps[] = $step->setStartDate($date); // Start step
        endif;
        
        // if (!$this->tracking->report):$this->tracking->report = new Report($this->tracking);endif; // Report file
        
        $this->tracking->run = true;
        $this->tracking->status = 'run';

        $this->session->create($this->tracking);

        return $this;
    }
    
    /**
     * Stop timer
     *
     * @return self
     */
    public function stop() {
        $this->tracking->run = false;
        $this->tracking->status = 'pause';
        if (!$this->tracking->end_date):
            $date = new DateTime();
            $end_date = $date->getTime();
            $this->tracking->end_date = $end_date;
        endif;

        // Stop last step
        $last_step = end($this->tracking->steps);
        $last_step->setEndDate();
        
        return $this;
    }
    
    /**
     * End timer
     *
     * @return self
     */
    public function end() {
        $this->stop($this->tracking);
        $this->tracking->status = 'archived';
        foreach (array_reverse($this->tracking->steps ?? []) as $step):
            if (!$step->commit):
                $step->commit = 'canceled';
                // unset($this->tracking->steps[$index]);
            endif;
        endforeach;
        $this->session->remove(); // Remove current session

        return $this;
    }

    /**
     * Create commit
     *
     * @param Commit $commit
     * @return self
     */
    public function commit(Commit $commit) {
        // Record Commit
        $this->tracking->commits[] = $commit;
        
        if ($this->tracking->run):
            // Start new Step
            $step = new Step();
            $this->tracking->steps[] = $step->setStartDate();
        endif;

        return $this;
    }

    /**
     * Delete Tracking & report file associated
     *
     * @return self
     */
    public function remove() {
        $tracking = new Tracking();
        if ($this->session && $this->session->info->id === $this->tracking->id):
            $this->session->remove();
        endif;
        $report = new Report($this->tracking);
        $report->remove();

        return $this;
    }

    /**
     * Generate Tracking Report
     *
     * @param Tracking $tracking
     * @return self
     */
    public function report() {
        $report = new Report($this->tracking);
        $json = json_encode($this->tracking);
        $report->write($json);

        return $this;
    }

    /**
     * Symfony Console Output
     *
     * @param $output
     * @return self
     */
    public function output($output) {
        $report = new Output($output);
        $report->render($this->tracking);

        return $this;
    }
}