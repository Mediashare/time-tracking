<?php

/*
 * This file is part of the Cilex framework.
 *
 * (c) Mike van Riel <mike.vanriel@naenius.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cilex\Command;

use Cilex\Service\Commit;
use Cilex\Service\Module;
use Cilex\Service\Report;
use Cilex\Service\Session;
use Cilex\Provider\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Example command for testing purposes.
 */
class TrackingCommitCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('commit')
            ->setDescription('Commit Time Tracking')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message write for this commit.') 
            ->addOption(
                'module',
                'm',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Module(s) executed with commit.'
            )
            ->addOption(
                'inject-variable',
                'i',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Variable(s) injected in module(s) with commit. Format: json {"module_name":{"variable_name":"variable_value"}}'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = new Session();
        $tracking = $session->getLast();
        if ($tracking):
            // Variable(s) Injected
            $variables = (array) $input->getOption('inject-variable');
            // Module(s) (exemple: src/Cilex/Modules/Git.sh)
            $command = new Module($variables);
            $modules = (array) $input->getOption('module');
            // Commit
            $message = $input->getArgument('message');
            $commit = new Commit($message, $modules);
            $tracking->commit($commit);
            
            // Module(s) execution
            foreach ($modules as $module) {
                $commit->commands[] = $command->execute($module);
            }

            // Output terminal
            $text = "[Commit] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Render Report
            $tracking->report->render($output, $tracking);
            // Json
            $json = json_encode($tracking);
            $tracking->report->write($json);
        endif;
    }
}
