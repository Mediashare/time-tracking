<?php
namespace Mediashare\Command;

use Mediashare\Service\Commit;
use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class CommitEditCommand extends Command {
    protected static $defaultName = 'timer:commit:edit';

    protected function configure() {
        $this
            ->setName('timer:commit:edit')
            ->setDescription('Edit commit')
            ->addArgument('id', InputArgument::REQUIRED, 'Commit id.')
            ->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'Message write for this commit.')
            ->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'Commit with custom duration. (+1minutes, +1hours, +1days)')
            ->addOption('timer-id', 'tid', InputOption::VALUE_REQUIRED, 'Commit Tracking by id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('timer-id') ?? null);
        
        if ($tracking):
            if ($input->getOption('message') || $input->getOption('message')):
                $controller = new Controller($tracking);
                // Get Commit
                $commit = new Commit($tracking);
                $commit = $commit->edit(
                    $input->getArgument('id'), 
                    $input->getOption('message'), 
                    $input->getOption('duration')
                );
                $controller->commit($commit);
                    
                // Output terminal
                $output->writeln('<info>[Tracking:'.$tracking->id.'] Edit commit</info>');
            endif;

            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        return 1;
    }
}
