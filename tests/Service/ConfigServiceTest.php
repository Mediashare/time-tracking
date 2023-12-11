<?php

namespace Mediashare\TimeTracking\Tests\Service;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Service\ConfigService;
use PHPUnit\Framework\TestCase;

class ConfigServiceTest extends TestCase {
    private ConfigService $configService;
    private string $tempConfigPath;

    protected function setUp(): void
    {
        $this->tempConfigPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'time-tracking' . DIRECTORY_SEPARATOR . 'temp_config.json';
        $this->configService = new ConfigService();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
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

    public function testGetLastTrackingDirectory()
    {
        // Create a config file with a specific tracking directory
        $config = $this->configService->createConfig($this->tempConfigPath, null, '/path/to/tracking');
        $lastTrackingDirectory = $this->configService->getLastTrackingDirectory();

        $this->assertEquals('/path/to/tracking', $lastTrackingDirectory);
    }

    public function testGetLastTrackingId()
    {
        // Create a config file with a specific tracking directory and ID
        $config = $this->configService->createConfig($this->tempConfigPath, null, '/path/to/tracking', '12345');
        $lastTrackingId = $this->configService->getLastTrackingId('/path/to/tracking');

        $this->assertEquals('12345', $lastTrackingId);
    }
}