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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Mediashare\Service\Session;
use Mediashare\Service\Commit;
use Mediashare\Service\Report;

/**
 * Example command for testing purposes.
 */
class TrackingStopCommand extends Command
{
    protected static $defaultName = 'timer:stop';
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Stop Time Tracking');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = new Session();
        $tracking = $session->getLast();
        if ($tracking):
            $tracking = $tracking->stop();

            // Output
            $text = "[Stop] Time Tracking - " . $tracking->id;
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
