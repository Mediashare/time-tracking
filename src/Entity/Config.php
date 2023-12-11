<?php

namespace Mediashare\TimeTracking\Entity;

class Config {
    public const CONFIG_PATH = '.'.DIRECTORY_SEPARATOR.'.time-tracking'.DIRECTORY_SEPARATOR.'config.json';
    public const TRACKING_DIRECTORY = '.'.DIRECTORY_SEPARATOR.'.time-tracking'.DIRECTORY_SEPARATOR.'trackings';

    public const DATETIME_FORMAT = 'd/m/Y H:i:s';

    public function __construct(
        private string|null $dateTimeFormat = self::DATETIME_FORMAT,
        private string|null $trackingDirectory = null,
        private string|null $trackingId = null,
    ) { }

    public function setTrackingDirectory(string $trackingDirectory): self {
        $this->trackingDirectory = $trackingDirectory;

        return $this;
    }

    public function getTrackingDirectory(): string|null {
        return $this->trackingDirectory ?? self::TRACKING_DIRECTORY;
    }

    public function setDateTimeFormat(string $dateTimeFormat): self {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getDateTimeFormat(): string {
        return $this->dateTimeFormat;
    }

    public function setTrackingId(string $trackingId): self {
        $this->trackingId = $trackingId;

        return $this;
    }

    public function getTrackingId(): string|null {
        return $this->trackingId;
    }

    public function toArray(): array {
        return [
            'trackingDirectory' => $this->getTrackingDirectory(),
            'dateTimeFormat' => $this->getDateTimeFormat(),
            'trackingId' => $this->getTrackingId(),
        ];
    }
}
