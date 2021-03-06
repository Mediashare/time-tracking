<?php
namespace Mediashare\Command;

use Mediashare\Service\Commit;
use Mediashare\Service\DateTime;
use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class CommitCommand extends Command {
    protected static $defaultName = 'timer:commit';

    protected function configure() {
        $this
            ->setName('timer:commit')
            ->setDescription('New commit')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message from this commit.')
            ->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'Custom duration from this commit. (+1minutes, +1hours, +1days)')
            ->addOption('timer-id', 'tid', InputOption::VALUE_REQUIRED, 'Commit from this Tracking id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('timer-id') ?? null);
        
        if ($tracking):
            $controller = new Controller($tracking);
            // Commit
            $commit = new Commit($tracking);
            $commit = $commit->create(
                $input->getArgument('message') ?? null,
                $input->getOption('duration') ?? null
            );
            $controller->commit($commit);

            // Output terminal
            $output->writeln('<info>[Tracking:'.$tracking->id.'] New commit</info>');
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        return 1;
    }
}
