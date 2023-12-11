<?php
namespace Mediashare\TimeTracking\Command;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Service\ConfigService;
use Mediashare\TimeTracking\Service\OutputService;
use Mediashare\TimeTracking\Service\SerializerService;
use Mediashare\TimeTracking\Service\TrackingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command {
    protected static $defaultName = 'timer:start';
    
    protected function configure() {
        $this
            ->setName('timer:start')
            ->setDescription('<comment>Starting</comment> time-tracking step selected')
            ->addArgument('name', InputArgument::OPTIONAL, 'Set the <comment>name</comment> of time-tracking selected', false)
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Starting time-tracking by <comment>ID</comment> selected')
            ->addOption('new', null, InputOption::VALUE_NONE, 'Starting <comment>new</comment> time-tracking')
            ->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'Set the <comment>duration</comment> of the current step (ex: "<comment>+1minutes</comment>", "<comment>+10min</comment>", "<comment>+1hours</comment>", "<comment>+1days</comment>", "<comment>-1hour</comment>")', false)

            // Config
            ->addOption('config-path', 'c', InputOption::VALUE_REQUIRED, 'Filepath to time-tracking json config file')
            ->addOption('config-datetime-format', 'cdf', InputOption::VALUE_REQUIRED, 'Set DateTime format (ex: <comment>"d/m/Y H:i:s"</comment>, <comment>"m/d/Y H:i:s"</comment>)', Config::DATETIME_FORMAT)
            ->addOption('config-tracking-dir', 'ctd', InputOption::VALUE_REQUIRED, 'Set directory path containing the time-tracking files')
            ->addOption('config-tracking-id', 'cti', InputOption::VALUE_REQUIRED, 'Time-tracking <comment>ID</comment> selected in config')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            // Config
            $configService = new ConfigService();
            if ($input->getOption('new')):
                $config = $configService->createConfig(
                    $input->getOption('config-path'),
                    $input->getOption('config-datetime-format'),
                    $input->getOption('config-tracking-dir'),
                    $input->getOption('id')
                        ?? $input->getOption('config-tracking-id')
                        ?? (new \DateTime())->format(
                            $input->getOption('config-datetime-format') ?? $configService->getLastDateTimeFormat()
                        ),
                );
            else:
                $config = $configService->createConfig(
                    $input->getOption('config-path'),
                    $input->getOption('config-datetime-format'),
                    $input->getOption('config-tracking-dir'),
                    $input->getOption('id') ?? $input->getOption('config-tracking-id'),
                );
            endif;

            // Tracking
            $trackingService = new TrackingService($config);
            $tracking = $trackingService->startTracking(
                $input->getArgument('name'),
                $input->getOption('duration'),
            );

            // Update tracking data file
            $serializerService = new SerializerService();
            $serializerService->writeTracking($trackingService->getTrackingFilepath(), $tracking);

            // Output render into terminal
            $output->writeln('<info>[Tracking:<comment>'.$tracking->getId().'</comment>] Starting time-tracking</info>');
            $outputService = new OutputService($output, $config);
            $outputService->renderCommits($tracking);
            $outputService->renderTrackings($tracking);

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
