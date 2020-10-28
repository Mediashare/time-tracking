<?php
namespace Mediashare\Service;
use Mediashare\Service\Tracking;
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
        // Commits
        $commits = $this->commits($tracking);
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Commits', ['colspan' => 5])],
                ['N°', 'ID', 'Message', 'Duration', 'Create date']
            ])
            ->setRows($commits)
            ->render();

        // Informations
        $seconds = 0;
        foreach (array_reverse($tracking->steps) as $step):
            if (!$step->commit):
                $duration = $step->getDuration();
                $parser = explode(':', $duration);
                $seconds += (($parser[0] ?? 0) * 60 * 60) + (($parser[1] ?? 0) * 60) + $parser[2] ?? 0;
            endif;
        endforeach;
        $current_step = sprintf('%02d:%02d:%02d', ($seconds/3600),($seconds/60%60), $seconds%60);

        // $last_step = end($tracking->steps);
        // if ($last_step && !$last_step->commit && !$last_step->end_date):
        //     // $last_step_duration = $last_step->getDuration();
        //     $last_step->stop();
        //     $current_step = $last_step->getDuration(false);
        // endif;
        $informations = [
            'id' => $tracking->id,
            'name' => $tracking->name,
            'running' => $tracking->getStatus(),
            'commits' => (string) count($tracking->commits),
            'duration' => $tracking->getDuration(),
            'current_timer' => $current_step,
            'date' => $tracking->getCreateDate()
        ];
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Tracking', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Create date']
            ])
            ->setRows([$informations])
            ->render();
    }

    public function arrayToObject(array $array, string $class_name) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen('Mediashare\Service\\' . $class_name),
            'Mediashare\Service\\' . $class_name,
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
            ];
        }
        return $commits;
    }
}
