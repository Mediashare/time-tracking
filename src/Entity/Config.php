<?php

namespace Mediashare\Marathon\Entity;

class Config {
    public const CONFIG_PATH = '.'.DIRECTORY_SEPARATOR.'.marathon'.DIRECTORY_SEPARATOR.'config.json';
    public const TIMER_DIRECTORY = '.'.DIRECTORY_SEPARATOR.'.marathon'.DIRECTORY_SEPARATOR.'timers';

    public const DATETIME_FORMAT = 'd/m/Y H:i:s';

    public function __construct(
        private string|null $dateTimeFormat = self::DATETIME_FORMAT,
        private string|null $timerDirectory = null,
        private string|null $timerId = null,
    ) { }

    public function setTimerDirectory(string $timerDirectory): self {
        $this->timerDirectory = $timerDirectory;

        return $this;
    }

    public function getTimerDirectory(): string|null {
        return $this->timerDirectory ?? self::TIMER_DIRECTORY;
    }

    public function setDateTimeFormat(string $dateTimeFormat): self {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getDateTimeFormat(): string {
        return $this->dateTimeFormat;
    }

    public function setTimerId(string $timerId): self {
        $this->timerId = $timerId;

        return $this;
    }

    public function getTimerId(): string|null {
        return $this->timerId;
    }

    public function toArray(): array {
        return [
            'timerDirectory' => $this->getTimerDirectory(),
            'dateTimeFormat' => $this->getDateTimeFormat(),
            'timerId' => $this->getTimerId(),
        ];
    }
}
