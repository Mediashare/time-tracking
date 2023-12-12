<?php

namespace Mediashare\Marathon\Tests\Service;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Service\ConfigService;
use Mediashare\Marathon\Tests\AbstractTestCase;

class ConfigServiceTest extends AbstractTestCase {
    private ConfigService $configService;
    private string $tempConfigPath;

    protected function setUp(): void
    {
        $this->tempConfigPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'marathon' . DIRECTORY_SEPARATOR . 'config.json';
        $this->configService = new ConfigService();
    }

    public function testCreateConfig()
    {
        $config = $this->configService->createConfig($this->tempConfigPath);

        $this->assertFileExists($this->tempConfigPath);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals(Config::DATETIME_FORMAT, $config->getDateTimeFormat());
    }

    public function testGetLastDateTimeFormat()
    {
        // Create a config file with a specific datetime format
        $config = $this->configService->createConfig($this->tempConfigPath, 'Y-m-d H:i:s');
        $lastDateTimeFormat = $this->configService->getLastDateTimeFormat();

        $this->assertEquals('Y-m-d H:i:s', $lastDateTimeFormat);
    }

    public function testGetLastTimerDirectory()
    {
        // Create a config file with a specific timer directory
        $config = $this->configService->createConfig($this->tempConfigPath, null, '/path/to/timer');
        $lastTimerDirectory = $this->configService->getLastTimerDirectory();

        $this->assertEquals('/path/to/timer', $lastTimerDirectory);
    }

    public function testGetLastTimerId()
    {
        // Create a config file with a specific timer directory and ID
        $config = $this->configService->createConfig($this->tempConfigPath, null, '/path/to/timer', '12345');
        $lastTimerId = $this->configService->getLastTimerId('/path/to/timer');

        $this->assertEquals('12345', $lastTimerId);
    }
}