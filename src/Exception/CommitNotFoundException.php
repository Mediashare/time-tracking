<?php

namespace Mediashare\TimeTracking\Exception;

class CommitNotFoundException extends \Exception {
    public function __construct(
        string $message = "Commit was not found",
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