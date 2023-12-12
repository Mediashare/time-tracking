<?php

namespace Mediashare\Marathon\Tests\Service;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Entity\Timer;
use Mediashare\Marathon\Exception\TimerNotFoundException;
use Mediashare\Marathon\Service\TimerService;
use Mediashare\Marathon\Tests\AbstractTestCase;

class TimerServiceTest extends AbstractTestCase {
    private TimerService $timerService;
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config(
            timerDirectory: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'marathon' . DIRECTORY_SEPARATOR . 'timers',
            timerId: (new \DateTime())->format('YmdHis')
        );
        $this->timerService = new TimerService($this->config);
    }

    public function testCreateTimer(): void {
        $timer = $this->timerService->createTimer(['id' => 'test_id']);

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertEquals('test_id', $timer->getId());
    }

    public function testGetTimer(): void {
        $timer = $this->timerService->getTimer();

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertTrue($timer->isRun());
    }

    public function testStartTimer(): void {
        $timer = $this->timerService->startTimer('Test Timer', '+1 hour');

        $this->assertTrue($timer->isRun());
        $this->assertEquals('Test Timer', $timer->getName());
        $this->assertCount(2, $timer->getSteps());

        $step = $timer->getSteps()->first();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertTrue($step->getEndDate() > $step->getStartDate());

        $step = $timer->getSteps()->last();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertNull($step->getEndDate());
    }

    public function testStopTimer(): void {
        $timer = $this->timerService->startTimer('Test Timer', '+1 hour');
        $timer = $this->timerService->stopTimer();

        $this->assertFalse($timer->isRun());
        $this->assertCount(2, $timer->getSteps());

        $step = $timer->getSteps()->first();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertTrue($step->getEndDate() > $step->getStartDate());

        $step = $timer->getSteps()->last();
        $this->assertInstanceOf(Step::class, $step);
        $this->assertSame($step->getEndDate(), $step->getStartDate());
    }

    public function testArchiveTimer(): void {
        $timer = $this->timerService->startTimer('Test Timer', '+1 hour');
        $timer = $this->timerService->archiveTimer();

        $this->assertTrue($timer->isArchived());
    }

    public function testRemoveTimer(): void {
        $timer = $this->timerService->startTimer('Test Timer', '+1 hour');
        $this->timerService->removeTimer();

        $this->expectException(TimerNotFoundException::class);
        $this->timerService->getTimer(createItIfNotExist: false);
    }
}