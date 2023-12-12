<?php

namespace Mediashare\Marathon\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Installer {
    public static function install(): void {
        $vendorDir = dirname(dirname(__DIR__)).'';
        $binDir = $vendorDir . '/bin';

        if ( !mkdir($binDir) && !is_dir($binDir)):
            throw new ProcessFailedException(sprintf('Directory "%s" was not created', $binDir));
        endif;

        $process = new Process(['cp', $vendorDir . '/mediashare/marathon/bin/time-tracker', $binDir . '/marathon']);
        $process->run();

        if (!$process->isSuccessful()):
            throw new ProcessFailedException($process);
        endif;

    }
}