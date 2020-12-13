<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class TimerListCommand extends Command {
    protected static $defaultName = 'timer:list';
    
    protected function configure() {
        $this
            ->setName('timer:list')
            ->setDescription('List all Time Tracking')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {   
        $tracking = new Tracking();
        $table = new Table($output);
        $table->setHeaders([
                [new TableCell('Trackings', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Create date']
            ])
            ->setRows($tracking->all())
            ->render();
        return 1;
    }
}
