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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Cilex\Provider\Console\Command;
use Cilex\Service\Session;
use Cilex\Service\DateTime;
use Cilex\Service\Tracking;
use Cilex\Service\Report;

/**
 * Example command for testing purposes.
 */
class TrackingStatusCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Status Time Tracking');
    }


    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = new Session();
        $tracking = $session->getLast();
        if ($tracking):
            // Output
            $text = "[Status] Time Tracking - " . $tracking->id;
            $output->writeln($text);
            // Render Report
            $tracking->report->render($output, $tracking);
            // Json
            $json = json_encode($tracking);
            $tracking->report->write($json);
        endif;
    }
}
