<?php
namespace Cilex\Service;
use Cilex\Service\Tracking;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
Class Report
{
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
            if ($file):
                $tracking_array = json_decode($file, true);
                if ($tracking_array):
                    $tracking = $this->arrayToObject($tracking_array, 'Tracking');
                    $tracking->report = $this->arrayToObject($tracking->report, 'Report');
                    foreach ($tracking->commits as $index => $commit):
                        $tracking->commits[$index] = $this->arrayToObject($commit, 'Commit');
                    endforeach;
                    foreach ($tracking->steps as $index => $step):
                        $tracking->steps[$index] = $this->arrayToObject($step, 'Step');
                    endforeach;
                    // Return Tracking
                    if (!empty($tracking)):return $tracking;endif;
                endif;
            endif;
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
                ['N°', 'ID', 'Message', 'Duration', 'Create date', 'Modules']
            ])
            ->setRows($commits)
            ->render();

        // Modules / Commands
        $commands = $this->commands($tracking);
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Commands', ['colspan' => 3])],
                ['Commit', 'Filename', 'Command', 'Result']
            ])
            ->setRows($commands)
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
                'date' => $commit->getCreateDate(),
                'modules' => implode(array_column($commit->commands, 'filename'), ', ')
            ];
        }
        return $commits;
    }

    private function commands(Tracking $tracking): array {
        // Commits
        $commands = [];
        foreach ($tracking->commits as $index => $commit) {
            foreach ((array) $commit->commands as $key => $command) {
                // Record
                $commands[] = [
                    new \Symfony\Component\Console\Helper\TableSeparator(),
                    [
                        'commit' => $commit->id,
                        'filename' => $command['filename'],
                        'command' => $command['content'],
                        'result' => $command['result'],
                    ]
                ];
            }
        }
        return $commands;
    }
}
