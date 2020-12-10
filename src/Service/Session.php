<?php
namespace Mediashare\Service;
use Mediashare\Entity\Report;
use Mediashare\Entity\Tracking;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

Class Session {
    public $file;
    public $dir = './.time-tracking/session';

    public function __construct() {
        $this->filesystem = new Filesystem();
        // Sessions Dir
        if (!$this->filesystem->exists($this->dir . '/')):$this->filesystem->mkdir($this->dir);endif;
    }

    /**
     * Get session by id
     *
     * @return object Tracking
     */
    public function getById(string $id) {
        // Remove old session
        $sessions = glob($this->dir . '/*');
        if (isset($sessions[0])):
            $session_id = basename($sessions[0]);
            if ($session_id !== $id):
                $this->remove();
            endif;
        endif;

        // Create session by Tracking id
        $tracking = new Tracking();
        $tracking->id = $id;
        $this->create($tracking); 
        // Get tracking report
        $report = new Report($tracking);
        $tracking = $report->read();

        if (!empty($tracking)):return $tracking;endif; 
        
        return false;
    }

    /**
     * Get last session
     *
     * @return object Tracking
     */
    public function getLast() {
        $sessions = glob($this->dir . '/*');
        if (isset($sessions[0])):
            $session_id = basename($sessions[0]);
            $tracking = new Tracking();
            $tracking->id = $session_id;
            $report = new Report($tracking);
            
            $tracking = $report->read();
            if (!empty($tracking)):return $tracking;endif; 
        endif;
        
        return false;
    }

    public function create(Tracking $tracking) {
        // Remove old session(s)
        $this->remove();
        
        // Create new file session
        $file = $this->dir . '/' . $tracking->id;
        if (!$this->filesystem->exists($file)):$this->filesystem->touch($file);endif;
        $this->file = $file;

        // Write basic informations
        $this->write($tracking->id);
        
        return true;
    }

    public function remove() {
        foreach (glob($this->dir . '/*') as $session) {
            $this->filesystem->remove($session);
        }
        return true;
    }

    private function write(string $id) {
        $date = new DateTime();
        $date = $date->getTime();
        $text = json_encode([
            'id' => $id,
            'date' => $date
        ]);
        $this->filesystem->appendToFile($this->file, $text);
        return true;
    }
}
