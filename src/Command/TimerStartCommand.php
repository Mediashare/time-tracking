<?php
namespace Mediashare\Marathon\Command;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Service\ConfigService;
use Mediashare\Marathon\Service\OutputService;
use Mediashare\Marathon\Service\SerializerService;
use Mediashare\Marathon\Service\TimerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimerStartCommand extends Command {
    protected static $defaultName = 'timer:start';
    
    protected function configure() {
        $this
            ->setName('timer:start')
            ->setDescription('<comment>Starting</comment> timer step selected')
            ->addArgument('name', InputArgument::OPTIONAL, 'Set the <comment>name</comment> of timer selected', false)
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Starting timer by <comment>ID</comment> selected')
            ->addOption('new', null, InputOption::VALUE_NONE, 'Starting <comment>new</comment> timer')
            ->addOption('duration', 'd', InputOption::VALUE_REQUIRED, 'Set the <comment>duration</comment> of the current step (ex: "<comment>+1minutes</comment>", "<comment>+10min</comment>", "<comment>+1hours</comment>", "<comment>+1days</comment>", "<comment>-1hour</comment>")', false)

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
            if ($input->getOption('new')):
                $config = $configService->createConfig(
                    $input->getOption('config-path'),
                    $input->getOption('config-datetime-format'),
                    $input->getOption('config-timer-dir'),
                    $input->getOption('id')
                        ?? $input->getOption('config-timer-id')
                        ?? (new \DateTime())->format(
                            $input->getOption('config-datetime-format') ?? $configService->getLastDateTimeFormat()
                        ),
                );
            else:
                $config = $configService->createConfig(
                    $input->getOption('config-path'),
                    $input->getOption('config-datetime-format'),
                    $input->getOption('config-timer-dir'),
                    $input->getOption('id') ?? $input->getOption('config-timer-id'),
                );
            endif;

            // Timer
            $timerService = new TimerService($config);
            $timer = $timerService->startTimer(
                $input->getArgument('name'),
                $input->getOption('duration'),
            );

            // Update timer data file
            $serializerService = new SerializerService();
            $serializerService->writeTimer($timerService->getTimerFilepath(), $timer);

            // Output render into terminal
            $output->writeln('<info>[Timer:<comment>'.$timer->getId().'</comment>] Starting timer</info>');
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
