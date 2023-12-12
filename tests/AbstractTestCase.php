<?php

namespace Mediashare\Marathon\Tests;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase {
    public function tearDown(): void {
        $this->rmdir(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'marathon');
    }

    private function rmdir(string $directory): void {
        if (is_dir($directory)) {
            $directories = scandir($directory);
            foreach ($directories as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($directory. DIRECTORY_SEPARATOR .$object) && !is_link($directory."/".$object))
                        $this->rmdir($directory. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($directory. DIRECTORY_SEPARATOR .$object);
                }
            }
            rmdir($directory);
        }
    }
}