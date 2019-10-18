<?php
namespace Cilex\Service;
use Cilex\Service\Tracking;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
Class Report
{
    public $file;

    public function __construct(Tracking $tracking = null) {
        $filesystem = new Filesystem();
        // Reports Dir
        $dir = './reports';
        if (!$filesystem->exists($dir . '/')):$filesystem->mkdir($dir);endif;
        
        // Report File
        $file = "report-";
        if ($tracking->name):
            // $file .= $tracking->name . '-';
        endif;
        $file .= $tracking->id . '.json';
        $this->file = $dir . '/' . $file;
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

    public function read() {
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->file)):
            $file = file_get_contents($this->file);
            if ($file):return $file;endif;
        endif;

        return false;
    }

    public function render(OutputInterface $output, Tracking $tracking) {
        // Informations
        $last_step_duration = '00:00:00';
        $last_step = end($tracking->steps);
        if ($last_step && !$last_step->commit):
            $last_step_duration = $last_step->getDuration();
        endif;
        $informations = [
            'id' => $tracking->id,
            'name' => $tracking->name,
            'running' => $tracking->getStatus(),
            'commits' => (string) count($tracking->commits),
            'last_step' => $last_step_duration,
            'duration' => $tracking->getDuration(),
            'date' => $tracking->getCreateDate()
        ];
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Tracking', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Last Step', 'Duration', 'Create date']
            ])
            ->setRows([$informations])
            ->render();

        // Commits
        $commits = $this->commits($tracking);
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Commits', ['colspan' => 5])],
                ['N°', 'ID', 'Message', 'Duration', 'Create date']
            ])
            ->setRows($commits)
            ->render();
    }

    public function arrayToObject(array $array, string $class_name) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen('Cilex\Service\\' . $class_name),
            'Cilex\Service\\' . $class_name,
            strstr(serialize($array), ':')
        ));
    }

    private function commits(Tracking $tracking): array {
        // Commits
        $commits = [];
        foreach ($tracking->commits as $index => $commit) {
            // Record
            $commits[] = [
                'index' => $index + 1,
                'id' => $commit->id,
                'message' => $commit->message,
                'duration' => $commit->getDuration(),
                'date' => $commit->getCreateDate()
            ];
        }
        return $commits;
    }
}
