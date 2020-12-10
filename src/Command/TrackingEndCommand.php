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

class TrackingEndCommand extends Command {
    protected static $defaultName = 'timer:end';
    
    protected function configure() {
        $this
            ->setName('end')
            ->setDescription('End Time Tracking. (Archive session)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'End Tracking by id.')
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
            $tracking->stop(); // Stop Tracking
            $json = json_encode($tracking);
            $tracking->report->write($json);
            $session->remove(); // Remove current session
            
            $text = "[End] Time Tracking - " . $tracking->id;
            $output->writeln($text);
        // Render Report
        $tracking->report->render($output, $tracking);
        endif;
        return 1;
    }
}
