<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class TimerEndCommand extends Command {
    protected static $defaultName = 'timer:end';
    
    protected function configure() {
        $this
            ->setName('timer:end')
            ->setDescription('End Time Tracking. (Archive session)')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'End Tracking by id.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $tracking = new Tracking();
        $tracking = $tracking->init($input->getOption('id') ?? null);

        if ($tracking):
            $controller = new Controller($tracking);
            $controller->end(); // Stop Tracking
            
            $text = "[End] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            
            // Report file creation
            $controller->report();
            // Render Report
            $controller->output($output);
        else: $output->writeln('<error>Tracking was not found.</error>'); endif;
        
        return 1;
    }
}
