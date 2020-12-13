<?php
namespace Mediashare\Service;

use Mediashare\Entity\Tracking;
use Mediashare\Service\DateTime;
use Symfony\Component\Filesystem\Filesystem;

Class Session {
    public $dir = './.time-tracking/session';
    public $file;
    public $info;

    public function __construct() {
        $this->filesystem = new Filesystem();
        // Sessions Dir
        if (!$this->filesystem->exists($this->dir . '/')):$this->filesystem->mkdir($this->dir);endif;
        
        $session = glob($this->dir . '/*');
        if (isset($session[0])):
            $this->info = \json_decode(\file_get_contents($session[0]));
        endif;
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
            'date' => $date,
            'report_file' => './.time-tracking/report-'.$id.'.json'
        ]);
        $this->filesystem->appendToFile($this->file, $text);
        return true;
    }
}
