<?php
namespace Mediashare\Service;

use Mediashare\Entity\Tracking;
use Mediashare\Service\Serializer;
use Symfony\Component\Filesystem\Filesystem;

Class Report {
    public $file;

    public function __construct(Tracking $tracking = null) {
        $filesystem = new Filesystem();
        // Reports Dir
        $dir = './.time-tracking';
        if (!$filesystem->exists($dir . '/')):$filesystem->mkdir($dir);endif;
        
        // Report File
        $file = "report-";
        if (!empty($tracking->id)):$file .= $tracking->id . '.json';
        else:$file .= uniqid() . '.json';endif;
        $this->file = $dir . '/' . $file;
    }

    public function read(?string $file = null) {
        $serializer = new Serializer();
        return $serializer->read($file);
    }

    public function create() {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->file)):
            $filesystem->touch($this->file);
        endif;
        return $this->file;
    }

    public function write(string $json) {
        $this->create();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $json);
    }

    public function remove() {
        $filesystem = new Filesystem();
        $filesystem->remove($this->file);
    }
}
