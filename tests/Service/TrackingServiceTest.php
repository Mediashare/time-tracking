<?php

namespace Mediashare\TimeTracking\Tests\Service;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Step;
use Mediashare\TimeTracking\Entity\Tracking;
use Mediashare\TimeTracking\Exception\TrackingNotFoundException;
use Mediashare\TimeTracking\Service\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackingServiceTest extends TestCase {
    private TrackingService $trackingService;
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config(
            trackingDirectory: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'time-tracking',
            trackingId: (new \DateTime())->format('YmdHis')
        );
        $this->trackingService = new TrackingService($this->config);
    }

    public function testCreateTracking(): void {
        $tracking = $this->trackingService->createTracking(['id' => 'test_id']);

        $this->assertInstanceOf(Tracking::class, $tracking);
        $this->assertEquals('test_id', $tracking->getId());
    }

    public function testGetTracking(): void {
        $tracking = $this->trackingService->getTracking();

        $this->assertInstanceOf(Tracking::class, $tracking);
        $this->assertTrue($tracking->isRun());
    }

    public function testStartTracking(): void {
        $tracking = $this->trackingService->startTracking('Test Tracking', '+1 hour');

        $this->assertTrue($tracking->isRun());
        $this->assertEquals('Test Tracking', $tracking->getName());
        $this->assertCount(2, $tracking->getSteps());

        $step = $tracking->getSteps()->first();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertTrue($step->getEndDate() > $step->getStartDate());

        $step = $tracking->getSteps()->last();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertNull($step->getEndDate());
    }

    public function testStopTracking(): void {
        $tracking = $this->trackingService->startTracking('Test Tracking', '+1 hour');
        $tracking = $this->trackingService->stopTracking();

        $this->assertFalse($tracking->isRun());
        $this->assertCount(2, $tracking->getSteps());

        $step = $tracking->getSteps()->first();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertTrue($step->getEndDate() > $step->getStartDate());

        $step = $tracking->getSteps()->last();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertSame($step->getEndDate(), $step->getStartDate());
    }

    public function testArchiveTracking(): void {
        $tracking = $this->trackingService->startTracking('Test Tracking', '+1 hour');
        $tracking = $this->trackingService->archiveTracking();

        $this->assertTrue($tracking->isArchived());
    }

    public function testRemoveTracking(): void {
        $tracking = $this->trackingService->startTracking('Test Tracking', '+1 hour');
        $this->trackingService->removeTracking();

        $this->expectException(TrackingNotFoundException::class);
        $this->trackingService->getTracking(createItIfNotExist: false);
    }
}