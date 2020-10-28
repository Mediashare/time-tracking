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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Example command for testing purposes.
 */
class TrackingUpgradeCommand extends Command
{
    protected static $defaultName = 'timer:upgrade';
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('upgrade')
            ->setDescription('Download latest version of Time Tracking');
    }


    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!\Phar::running()):
            $text = "Use <git pull> for upgrade Time Tracking";
            $output->writeln($text);
            return 1;
        endif;
        $file = \Phar::running();
        $file = str_replace('phar://', '', $file);
        $url = 'https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking.phar';
        file_put_contents($tmp = $file.'.tmp', file_get_contents($url));
        if (\file_exists($tmp)):
            $filesystem = new Filesystem();
            $filesystem->remove($file);
            $filesystem->rename($tmp, $file);
            $filesystem->chmod($file, 0755);

            $text = "Time Tracking successly updated";
            $output->writeln($text);
        else:
            $text = "Error download [https://raw.githubusercontent.com/Mediashare/time-tracking/master/time-tracking.phar]";
            $output->writeln($text);
        endif;

        return 1;
    }
}
