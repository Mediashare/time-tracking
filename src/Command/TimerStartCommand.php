<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class TimerStartCommand extends Command {
    protected static $defaultName = 'timer:start';
    
    protected function configure() {
        $this
            ->setName('timer:start')
            ->setDescription('Start timer')
            ->addArgument('name', InputArgument::OPTIONAL, 'Timer name')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Start timer by id')
            ->addOption('new', null, InputOption::VALUE_NONE, 'Start new timer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$input->getOption('new')):
            $tracking = new Tracking();
            $tracking = $tracking->init($input->getOption('id') ?? null);
        endif;

        if (empty($tracking)): // Session not found
            $tracking = new Tracking();
            $tracking = $tracking->create($input->getOption('id') ?? null, $input->getArgument('name')); // Create Tracking
        endif;

        $controller = new Controller($tracking);
        $controller->start(); // Start Tracking

        // Output
        $output->writeln('<info>[Tracking:'.$tracking->id.'] Start</info>');
        // Report file creation
        $controller->report();
        // Render Report
        $controller->output($output);
        return 1;
    }
}
