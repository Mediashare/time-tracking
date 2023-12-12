<?php

namespace Mediashare\Marathon\Service;

use Symfony\Component\Filesystem\Filesystem;
use Mediashare\Marathon\Entity\Config;

class ConfigService {
    private SerializerService $serializerService;
    private Filesystem $filesystem;

    private string $configPath = Config::CONFIG_PATH;
    private Config $config;

    public function __construct(
    ) {
        $this->serializerService = new SerializerService();
        $this->filesystem = new Filesystem();
    }

    public function createConfig(
        string|null $configPath = null,
        string|null $dateTimeFormat = null,
        string|null $timerDirectory = null,
        string|null $timerId = null,
    ): Config {
        $configPath ? $this->configPath = $configPath : null;

        $this->config = new Config(
            $dateTimeFormat = $dateTimeFormat ?? $this->getLastDateTimeFormat(),
            $timerDirectory = $timerDirectory ?? $this->getLastTimerDirectory(),
            $timerId
                ?? $this->getLastTimerId($timerDirectory)
                ?? (new \DateTime())->format('YmdHis')
            ,
        );

        $this->createConfigFile();

        return $this->config;
    }

    public function isDebug(): bool {
        return (empty($_ENV['APP_ENV']) || strtolower($_ENV['APP_ENV']) !== 'prod');
    }

    public function getLastDateTimeFormat(): string {
        return $this->getLastConfig()->getDateTimeFormat();
    }

    public function getLastTimerDirectory(): string {
        return $this->getLastConfig()->getTimerDirectory();
    }

    public function getLastTimerId(string $timerDirectory): string|null {
        try {
            if ($lastTimerIdByConfig = $this->getLastConfig()->getTimerId()):
                return $lastTimerIdByConfig;
            endif;

            return (new TimerService(new Config(timerDirectory: $timerDirectory)))
                ->getTimers()?->last()?->getId();

        } catch (\Exception $exception) {

        }

        return null;
    }

    private function getLastConfig(): Config {
        return $this->filesystem->exists($this->configPath)
            ? $this->serializerService->read($this->configPath, Config::class)
            : new Config()
        ;
    }

    private function createConfigFile(): self {
        $this->filesystem->dumpFile($this->configPath, json_encode($this->config->toArray(), JSON_THROW_ON_ERROR));

        return $this;
    }
}