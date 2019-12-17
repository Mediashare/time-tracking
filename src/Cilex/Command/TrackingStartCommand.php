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
use Cilex\Service\Tracking;
use Cilex\Service\Session;
use Cilex\Service\Report;
/**
 * Example command for testing purposes.
 */
class TrackingStartCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Start Time Tracking')
            ->addArgument('name', InputArgument::OPTIONAL, 'Project name.')
            // ->addOption('id', null, null, 'Start Tracking by id.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = new Session();
        $tracking = $session->getLast(); // Get current Tracking session
        if (!$tracking):
            $tracking = new Tracking(); // Create Tracking
            $tracking->name = $input->getArgument('name');
            $tracking->start(); // Start Tracking
            $session->create($tracking); // Create Tracking session
        else:
            $tracking->start(); // Start Tracking
        endif;
        
        // Output
        $text = "[Start] Time Tracking - " . $tracking->id;
        $output->writeln($text);
        // Render Report
        $tracking->report->render($output, $tracking);
        // Json creation
        $json = json_encode($tracking);
        $tracking->report->write($json);
    }
}
