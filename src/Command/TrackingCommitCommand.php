<?php
namespace Mediashare\Command;

use Mediashare\Entity\Commit;
use Mediashare\Entity\Report;
use Mediashare\Service\Session;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackingCommitCommand extends Command {
    protected static $defaultName = 'timer:commit';

    protected function configure() {
        $this
            ->setName('commit')
            ->setDescription('Commit Tracking')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message write for this commit.')
            ->addOption('tracking-id', 'tid', InputOption::VALUE_REQUIRED, 'Commit Tracking by id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $session = new Session();
        if ($input->getOption('tracking-id')):
            $tracking = $session->getById($input->getOption('tracking-id'));
        else:
            $tracking = $session->getLast(); // Get current Tracking session
        endif;

        if ($tracking):
            // Commit
            $message = $input->getArgument('message');
            $commit = new Commit($message);
            $tracking->commit($commit);

            // Output terminal
            $text = "[Commit] Time Tracking - " . $tracking->id;
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
