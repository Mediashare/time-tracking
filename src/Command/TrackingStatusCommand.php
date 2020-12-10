<?php
namespace Mediashare\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Mediashare\Service\DateTime;
use Mediashare\Service\Session;
use Mediashare\Entity\Tracking;
use Mediashare\Entity\Report;

class TrackingStatusCommand extends Command {
    protected static $defaultName = 'timer:status';
    
    protected function configure() {
        $this
            ->setName('status')
            ->setDescription('Status Time Tracking')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Status Tracking by id.')
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
            // Output
            $text = "[Status] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Render Report
            $tracking->report->render($output, $tracking);
            // Json
            $json = json_encode($tracking);
            $tracking->report->write($json);
        endif;
        return 1;
    }
}
