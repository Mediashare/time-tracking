<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class StopCommand extends Command {
    protected static $defaultName = 'timer:stop';
    
    protected function configure() {
        $this
            ->setName('timer:stop')
            ->setDescription('Stop timer')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Stop timer by id.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('id') ?? null);

        if ($tracking):
            $controller = new Controller($tracking);
            $controller->stop();
            // Output
            $output->writeln('<info>[Tracking:'.$tracking->id.'] Stop</info>');
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        
        return 1;
    }
}
