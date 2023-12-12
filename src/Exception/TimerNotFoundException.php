<?php

namespace Mediashare\Marathon\Exception;

class TimerNotFoundException extends \Exception {
    public function __construct(
        string $message = "Timer session was not found",
        int $code = 404,
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}