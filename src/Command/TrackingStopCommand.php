<?php
namespace Mediashare\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Mediashare\Service\Session;
use Mediashare\Entity\Report;
use Mediashare\Entity\Commit;

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
        $session = new Session();
        if ($input->getOption('id')):
            $tracking = $session->getById($input->getOption('id'));
        else:
            $tracking = $session->getLast(); // Get current Tracking session
        endif;

        if ($tracking):
            $tracking = $tracking->stop();
            // Output
            $text = "[Stop] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Render Report
            $tracking->report->render($output, $tracking);
            // Json
            $json = json_encode($tracking);
            $tracking->report->write($json);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        
        return 1;
    }
}
