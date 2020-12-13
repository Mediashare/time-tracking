<?php
namespace Mediashare\Command;

use Mediashare\Entity\Commit;
use Mediashare\Entity\Report;
use Mediashare\Service\Session;
use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackingStopCommand extends Command {
    protected static $defaultName = 'timer:stop';
    
    protected function configure() {
        $this
            ->setName('stop')
            ->setDescription('Stop Tracking')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Stop Tracking by id.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('id') ?? null);

        if ($tracking):
            $controller = new Controller($tracking);
            $controller->stop();
            // Output
            $text = "[Stop] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        
        return 1;
    }
}
