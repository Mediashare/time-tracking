<?php

namespace Mediashare\TimeTracking\Exception;

class TrackingNotFoundException extends \Exception {
    public function __construct(
        string $message = "Tracking session was not found",
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