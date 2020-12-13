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
     * Start Tracking
     *
     * @return object Tracking
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

        $this->session->create($this->tracking);
    }
    
    /**
     * Stop Tracking
     *
     * @return object Tracking
     */
    public function stop() {
        $this->tracking->run = false;
        if (!$this->tracking->end_date):
            $date = new DateTime();
            $end_date = $date->getTime();
            $this->tracking->end_date = $end_date;
        endif;

        // Stop last step
        $last_step = end($this->tracking->steps);
        $last_step->setEndDate();
    }
    
    /**
     * End Tracking
     */
    public function end() {
        $this->stop($this->tracking);
        $this->session->remove(); // Remove current session
    }

    /**
     * Commit Tracking
     *
     * @param Commit $commit
     */
    public function commit(Commit $commit) {
        // Record Commit
        $this->tracking->commits[] = $commit;
        
        if ($this->tracking->run):
            // Start new Step
            $step = new Step();
            $this->tracking->steps[] = $step->setStartDate();
        endif;
    }

    /**
     * Delete Tracking & report file associated
     *
     * @return void
     */
    public function remove() {
        $tracking = new Tracking();
        if ($this->session && $this->session->info->id === $this->tracking->id):
            $this->session->remove();
        endif;
        $report = new Report($this->tracking);
        $report->remove();
    }

    /**
     * Generate Tracking Report
     *
     * @param Tracking $tracking
     * @return void
     */
    public function report() {
        $report = new Report($this->tracking);
        $json = json_encode($this->tracking);
        $report->write($json);
    }

    /**
     * Symfony Console Output
     *
     * @param $output
     * @return void
     */
    public function output($output) {
        $report = new Output($output);
        $report->render($this->tracking);
    }
}