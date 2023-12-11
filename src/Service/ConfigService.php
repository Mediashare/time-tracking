<?php

namespace Mediashare\TimeTracking\Service;

use Symfony\Component\Filesystem\Filesystem;
use Mediashare\TimeTracking\Entity\Config;

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
        string|null $trackingDirectory = null,
        string|null $trackingId = null,
    ): Config {
        $configPath ? $this->configPath = $configPath : null;

        $this->config = new Config(
            $dateTimeFormat = $dateTimeFormat ?? $this->getLastDateTimeFormat(),
            $trackingDirectory = $trackingDirectory ?? $this->getLastTrackingDirectory(),
            $trackingId
                ?? $this->getLastTrackingId($trackingDirectory)
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

    public function getLastTrackingDirectory(): string {
        return $this->getLastConfig()->getTrackingDirectory();
    }

    public function getLastTrackingId(string $trackingDirectory): string|null {
        try {
            if ($lastTrackingIdByConfig = $this->getLastConfig()->getTrackingId()):
                return $lastTrackingIdByConfig;
            endif;

            return (new TrackingService(new Config(trackingDirectory: $trackingDirectory)))
                ->getTrackings()?->last()?->getId();

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