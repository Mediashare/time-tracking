<?php
namespace Mediashare\Command;

use Mediashare\Entity\Report;
use Mediashare\Entity\Tracking;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackingAllCommand extends Command {
    protected static $defaultName = 'timer:all';
    
    protected function configure() {
        $this
            ->setName('all')
            ->setDescription('List all Time Tracking')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {   
        foreach (glob('./.time-tracking/report-*') as $report):
            $tracking_id = str_replace('report-', '', \basename($report));
            $tracking_id = str_replace('.json', '', $tracking_id);
            
            $tracking = new Tracking();
            $tracking->id = $tracking_id;
            $report = new Report($tracking);
            // Informations
            $informations[] = $report->informations($report->read());
            
        endforeach;
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Tracking', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Create date']
            ])
            ->setRows($informations)
            ->render();
        return 1;
    }
}
