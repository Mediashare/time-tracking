<?php

namespace Symfony\Contracts\Service\Test;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase {
    public function tearDown(): void
    {
        rmdir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'time-tracking');
    }
}