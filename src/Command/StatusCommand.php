<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class StatusCommand extends Command {
    protected static $defaultName = 'timer:status';
    
    protected function configure() {
        $this
            ->setName('timer:status')
            ->setDescription('Status timer')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Status timer by id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('id') ?? null);

        if ($tracking):
            $controller = new Controller($tracking);
            // Output
            $output->writeln('<info>[Tracking:'.$tracking->id.'] Status</info>');
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        
        return 1;
    }
}
