<?php

/*
 * This file is part of the Cilex framework.
 *
 * (c) Mike van Riel <mike.vanriel@naenius.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mediashare\Command;

use Mediashare\Service\Commit;
use Mediashare\Service\Report;
use Mediashare\Service\Session;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Example command for testing purposes.
 */
class TrackingCommitCommand extends Command
{
    protected static $defaultName = 'timer:commit';
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('commit')
            ->setDescription('Commit Time Tracking')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message write for this commit.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = new Session();
        $tracking = $session->getLast();
        if ($tracking):
            // Commit
            $message = $input->getArgument('message');
            $commit = new Commit($message);
            $tracking->commit($commit);

            // Output terminal
            $text = "[Commit] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Render Report
            $tracking->report->render($output, $tracking);
            // Json
            $json = json_encode($tracking);
            $tracking->report->write($json);
        endif;
        return 1;
    }
}
