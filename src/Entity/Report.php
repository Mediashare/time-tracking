<?php
namespace Mediashare\Entity;
use Mediashare\Entity\Tracking;
use Mediashare\Service\Duration;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
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
                    foreach ($tracking->commits ?? [] as $index => $commit):
                        $tracking->commits[$index] = $this->arrayToObject($commit, 'Commit');
                        if (!empty($tracking->commits[$index]->step)):
                            $tracking->commits[$index]->step = $this->arrayToObject($tracking->commits[$index]->step, 'Step');
                        else:
                            foreach ($tracking->commits[$index]->steps ?? [] as $step_index => $step):
                                $tracking->commits[$index]->steps[$step_index] = $this->arrayToObject($step, 'Step');
                            endforeach;
                        endif;
                    endforeach;
                    foreach ($tracking->steps ?? [] as $index => $step):
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
                ['N°', 'ID', 'Message', 'Duration', 'Total', 'Create date']
            ])
            ->setRows($commits)
            ->render();

        // Current Step
        $current_step = new Duration();
        foreach (array_reverse($tracking->steps) as $step):
            if (!$step->commit):
                $current_step->addStep($step);
            endif;
        endforeach;
        
        // Informations
        $informations = [
            'id' => $tracking->id,
            'name' => $tracking->name,
            'running' => $tracking->getStatus(),
            'commits' => (string) count($tracking->commits),
            'duration' => $tracking->getDuration(),
            'current_timer' => $current_step->getDuration(),
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
        $serialized = unserialize(sprintf('O:%d:"%s"%s', strlen('Mediashare\Entity\\' . $class_name), 'Mediashare\Entity\\' . $class_name, strstr(serialize($array), ':')));
        return $serialized;
    }

    private function commits(Tracking $tracking): array {
        // Commits
        $commits = [];
        $duration_total = new Duration();
        foreach ($tracking->commits as $index => $commit) {
            if (!empty($commit->step)):
                $duration_total->addStep($commit->step);
            else:
                foreach ($commit->steps ?? [] as $step):
                    $duration_total->addStep($step);
                endforeach;
            endif;
            // Record
            $commits[] = [
                'index' => $index + 1,
                'id' => $commit->id,
                'message' => $commit->message,
                'duration' => $commit->getDuration(),
                'duration_total' => $duration_total->getDuration(),
                'date' => $commit->getCreateDate(),
            ];
        }
        return $commits;
    }
}
