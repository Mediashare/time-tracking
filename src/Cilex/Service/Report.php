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

    public function __construct(string $id) {
        $filesystem = new Filesystem();
        // Reports Dir
        if (!$filesystem->exists('./reports/')):$filesystem->mkdir('./reports');endif;
        // Report File
        $this->file = './reports/report-' . $id . '.json';
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
        // $filesystem->appendToFile($this->file, $json);
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
        if ($last_step):
            $last_step_duration = $last_step->getDuration();
        endif;
        $informations = [
            'id' => $tracking->id,
            'running' => $tracking->getStatus(),
            'commits' => (string) count($tracking->commits),
            'last_step' => $last_step_duration,
            'duration' => $tracking->getDuration(),
            'date' => $tracking->getCreateDate()
        ];
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Tracking', ['colspan' => 4])],
                ['ID', 'Status', 'Commits', 'Last Step', 'Duration', 'Create date']
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
