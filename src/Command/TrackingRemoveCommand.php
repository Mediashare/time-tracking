<?php
namespace Mediashare\Command;

use Mediashare\Entity\Report;
use Mediashare\Entity\Tracking;
use Mediashare\Service\Session;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackingRemoveCommand extends Command {
    protected static $defaultName = 'timer:remove';
    
    protected function configure() {
        $this
            ->setName('remove')
            ->setDescription('Remove Time Tracking')
            ->addArgument('id', InputArgument::REQUIRED, 'Id Time Tracking.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking_id = $input->getArgument('id');
        $session = new Session();
        $current_session = $session->getLast();
        if ($current_session && $current_session->id === $tracking_id):
            $session->remove();
            $output->writeln('<comment>Current session is reset.</comment>');
        endif;
        
        $tracking = new Tracking();
        $tracking->id = $tracking_id;
        $report = new Report($tracking);
        if (file_exists($report->file)):
            $report->remove();
            $output->writeln('<info>This Tracking was removed.</info>');
        else: $output->writeln('<error>This Tracking was not found.</error>'); endif;

        return 1;
    }
}
