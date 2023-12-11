<?php

namespace Mediashare\TimeTracking\Exception;

class FileNotFoundException extends \Exception {
    public function __construct(
        string $filepath,
        string $message = "File not found",
        int $code = 404,
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            '[<comment>'.$filepath.'</comment>] ' . $message,
            $code,
            $previous
        );
    }
}