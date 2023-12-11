<?php
namespace Mediashare\TimeTracking\Command;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Service\ConfigService;
use Mediashare\TimeTracking\Service\OutputService;
use Mediashare\TimeTracking\Service\SerializerService;
use Mediashare\TimeTracking\Service\TrackingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArchiveCommand extends Command {
    protected static $defaultName = 'timer:archive';
    
    protected function configure() {
        $this
            ->setName('timer:archive')
            ->setDescription('<comment>Archiving</comment> the time-tracking selected')
            ->addArgument('id', InputArgument::OPTIONAL, 'Time-tracking <comment>ID</comment> selected')
            ->addOption('stop', 's', InputOption::VALUE_NONE, '<comment>Stop</comment> current step of time-tracking')

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
            $config = $configService->createConfig(
                $input->getOption('config-path'),
                $input->getOption('config-datetime-format'),
                $input->getOption('config-tracking-dir'),
                $input->getArgument('id') ?? $input->getOption('config-tracking-id'),
            );

            // Tracking
            $trackingService = new TrackingService($config, createItIfNotExist: !$input->getOption('stop'));
            $tracking = $trackingService->archiveTracking();

            // Update tracking data file
            $serializerService = new SerializerService();
            $serializerService->writeTracking($trackingService->getTrackingFilepath(), $tracking);

            // Output render into terminal
            $output->writeln('<info>[Tracking:<comment>'.$tracking->getId().'</comment>] Archiving</info>');
            $outputService = new OutputService($output, $config);
            $outputService->renderCommits($tracking);
            $outputService->renderTrackings($tracking);

            // Update config
            $lastTrackingId = $configService->getLastTrackingId(
                $lastTrackingDirectory = $input->getOption('config-tracking-dir')
                    ?? $configService->getLastTrackingDirectory(),
            );

            $configService->createConfig(
                $input->getOption('config-path'),
                $input->getOption('config-datetime-format'),
                $lastTrackingDirectory,
                $config->getTrackingId() === $lastTrackingId
                    ? (new \DateTime())->format('YmdHis')
                    : $lastTrackingId
                ,
            );

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
