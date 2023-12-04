<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class CommitRemoveCommand extends Command {
    protected static $defaultName = 'timer:commit:remove';

    protected function configure() {
        $this
            ->setName('timer:commit:remove')
            ->setDescription('Remove commit')
            ->addArgument('id', InputArgument::REQUIRED, 'Commit id.')
            ->addOption('timer-id', 'tid', InputOption::VALUE_REQUIRED, 'Commit Tracking by id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('timer-id') ?? null);
        
        if ($tracking):
            $controller = new Controller($tracking);
            // Get Commit
            foreach ($tracking->commits ?? [] as $index => $commit):
                if ($commit->id === $input->getArgument('id')):
                    unset($tracking->commits[$index]);
                endif;
            endforeach;
            $tracking->commits = array_values($tracking->commits);

            // Output terminal
            $output->writeln('<info>[Tracking:'.$tracking->id.'] Remove commit</info>');
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else:
            $output->writeln('<error>Tracking was not found.</error>');
            return Command::FAILURE;
        endif;

        return Command::SUCCESS;
    }
}
