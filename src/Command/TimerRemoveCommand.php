<?php
namespace Mediashare\Command;

use Mediashare\Service\Tracking;
use Mediashare\Service\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

Class TimerRemoveCommand extends Command {
    protected static $defaultName = 'timer:remove';
    
    protected function configure() {
        $this
            ->setName('timer:remove')
            ->setDescription('Remove timer')
            ->addArgument('id', InputArgument::REQUIRED, 'Timer id')
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

            $output->writeln('<info>[Tracking:'.$tracking->id ?? $selected->id.'] Removed</info>');
        else: $output->writeln('<error>This Tracking was not found.</error>'); endif;

        return 1;
    }
}
