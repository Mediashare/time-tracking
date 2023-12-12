<?php
namespace Mediashare\Marathon\Command;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Service\ConfigService;
use Mediashare\Marathon\Service\OutputService;
use Mediashare\Marathon\Service\SerializerService;
use Mediashare\Marathon\Service\TimerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimerArchiveCommand extends Command {
    protected static $defaultName = 'timer:archive';
    
    protected function configure() {
        $this
            ->setName('timer:archive')
            ->setDescription('<comment>Archiving</comment> the timer selected')
            ->addArgument('id', InputArgument::OPTIONAL, 'Timer <comment>ID</comment> selected')
            ->addOption('stop', 's', InputOption::VALUE_NONE, '<comment>Stop</comment> current step of timer')

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
                $input->getArgument('id') ?? $input->getOption('config-timer-id'),
            );

            // Timer
            $timerService = new TimerService($config, createItIfNotExist: !$input->getOption('stop'));
            $timer = $timerService->archiveTimer();

            // Update timer data file
            $serializerService = new SerializerService();
            $serializerService->writeTimer($timerService->getTimerFilepath(), $timer);

            // Output render into terminal
            $output->writeln('<info>[Timer:<comment>'.$timer->getId().'</comment>] Archiving</info>');
            $outputService = new OutputService($output, $config);
            $outputService->renderCommits($timer);
            $outputService->renderTimers($timer);

            // Update config
            $lastTimerId = $configService->getLastTimerId(
                $lastTimerDirectory = $input->getOption('config-timer-dir')
                    ?? $configService->getLastTimerDirectory(),
            );

            $configService->createConfig(
                $input->getOption('config-path'),
                $input->getOption('config-datetime-format'),
                $lastTimerDirectory,
                $config->getTimerId() === $lastTimerId
                    ? (new \DateTime())->format('YmdHis')
                    : $lastTimerId
                ,
            );

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
