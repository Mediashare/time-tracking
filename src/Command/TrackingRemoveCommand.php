<?php
namespace Mediashare\Command;

use Mediashare\Service\Report;
use Mediashare\Service\Session;
use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
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
        $tracking = new Tracking();
        $current = $tracking->getLast();
        $selected = $tracking->get($input->getArgument('id'));
        if ($tracking):
            if ($current && $current->id !== $selected->id):
                $tracking->get($current->id);
            endif;

            $controller = new Controller($selected);
            $controller->remove();

            $output->writeln('<info>This Tracking was removed.</info>');
        else: $output->writeln('<error>This Tracking was not found.</error>'); endif;

        return 1;
    }
}
