<?php
namespace Mediashare\TimeTracking\Command;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Service\CommitService;
use Mediashare\TimeTracking\Service\ConfigService;
use Mediashare\TimeTracking\Service\OutputService;
use Mediashare\TimeTracking\Service\SerializerService;
use Mediashare\TimeTracking\Service\TrackingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommitEditCommand extends Command {
    protected static $defaultName = 'timer:commit:edit';

    protected function configure() {
        $this
            ->setName('timer:commit:edit')
            ->setDescription('<comment>Editing</comment> the commit from time-tracking selected')
            ->addArgument('id', InputArgument::REQUIRED, 'Commit <comment>ID</comment> selected (if not specified then retrieve last commit)')
            ->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'Update a commit <comment>message</comment>', false)
            ->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'Update the <comment>duration</comment> of the selected commit (ex: "<comment>+1minutes</comment>", "<comment>+10min</comment>", "<comment>+1hours</comment>", "<comment>+1days</comment>", "<comment>-1hour</comment>")', false)

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
                $input->getOption('config-tracking-id'),
            );

            // Commit
            $commitService = new CommitService($config);
            $tracking = $commitService->editCommit(
                $input->getArgument('id'),
                $input->getOption('message'),
                $input->getOption('duration'),
            );

            // Output terminal
            $output->writeln('<info>[Tracking:<comment>'.$tracking->getId().'</comment>] Editing commit</info>');

            // Update tracking data file
            $serializerService = new SerializerService();
            $serializerService->writeTracking((new TrackingService($config))->getTrackingFilepath(), $tracking);

            // Output render into terminal
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
