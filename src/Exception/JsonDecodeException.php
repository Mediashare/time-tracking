<?php

namespace Mediashare\Marathon\Exception;

class JsonDecodeException extends \Exception {
    public function __construct(
        string $filepath,
        string $message = "Json format is corrupted",
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