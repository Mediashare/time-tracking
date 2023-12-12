<?php
namespace Mediashare\Marathon\Command;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Service\CommitService;
use Mediashare\Marathon\Service\ConfigService;
use Mediashare\Marathon\Service\OutputService;
use Mediashare\Marathon\Service\SerializerService;
use Mediashare\Marathon\Service\TimerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommitRemoveCommand extends Command {
    protected static $defaultName = 'timer:commit:remove';

    protected function configure() {
        $this
            ->setName('timer:commit:remove')
            ->setDescription('<comment>Removing</comment> the commit from timer selected')
            ->addArgument('id', InputArgument::REQUIRED, 'Commit <comment>ID</comment> selected (if not specified then retrieve last commit)')

            // Config
            ->addOption('config-path', 'c', InputOption::VALUE_REQUIRED, 'Config path to json file')
            ->addOption('config-datetime-format', 'cdf', InputOption::VALUE_REQUIRED, 'Set DateTime format (ex: <comment>"d/m/Y H:i:s"</comment>, <comment>"m/d/Y H:i:s"</comment>)', Config::DATETIME_FORMAT)
            ->addOption('config-timer-dir', 'ctd', InputOption::VALUE_REQUIRED, 'Set directory path containing the timer files')
            ->addOption('config-timer-id', 'cti', InputOption::VALUE_REQUIRED, 'Timer <comment>ID</comment> selected in config')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            // Config
            $configService = new ConfigService();
            $config = $configService->createConfig(
                $input->getOption('config-path'),
                $input->getOption('config-datetime-format'),
                $input->getOption('config-timer-dir'),
                $input->getOption('config-timer-id'),
            );

            // Commit
            $commitService = new CommitService($config);
            $timer = $commitService->removeCommit($input->getArgument('id'));

            // Output terminal
            $output->writeln('<info>[Timer:<comment>'.$timer->getId().'</comment>] Removing commit</info>');

            // Update timer data file
            $serializerService = new SerializerService();
            $serializerService->writeTimer((new TimerService($config))->getTimerFilepath(), $timer);

            // Output render into terminal
            $outputService = new OutputService($output, $config);
            $outputService->renderCommits($timer);
            $outputService->renderTimers($timer);

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
