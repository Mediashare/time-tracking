<?php

namespace Mediashare\Marathon\Tests\Service;

use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Service\StepService;
use Mediashare\Marathon\Tests\AbstractTestCase;

class StepServiceTest extends AbstractTestCase {
    private StepService $stepService;

    public function setUp(): void {
        $this->stepService = new StepService();
    }

    public function testCreateStepWithNoDates(): void {
        $step = $this->stepService->createStep();

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertNull($step->getEndDate());
    }

    public function testCreateStepWithCustomStartDate(): void {
        $customStartDate = strtotime('2023-01-01');
        $step = $this->stepService->createStep($customStartDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($customStartDate, $step->getStartDate());
        $this->assertNull($step->getEndDate());
    }

    public function testCreateStepWithEndDate(): void {
        $endDate = strtotime('2023-02-01');
        $step = $this->stepService->createStep(null, $endDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertEquals($endDate, $step->getEndDate());
    }

    public function testCreateStepWithCustomDuration(): void {
        $customDuration = '+5 minutes';
        $step = $this->stepService->createStepWithCustomDuration($customDuration);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertNotNull($step->getStartDate());
        $this->assertNotNull($step->getEndDate());
        $this->assertGreaterThan($step->getStartDate(), $step->getEndDate());
        $this->assertEquals('00:05:00', $step->getDuration());
    }

    public function testCreateStepWithCustomDurationAndStartDate(): void {
        $customDuration = '+2 hours';
        $customStartDate = strtotime('2023-03-01');
        $step = $this->stepService->createStepWithCustomDuration($customDuration, $customStartDate);

        $this->assertInstanceOf(Step::class, $step);
        $this->assertEquals($customStartDate, $step->getStartDate());
        $this->assertNotNull($step->getEndDate());
        $this->assertGreaterThan($step->getStartDate(), $step->getEndDate());
        $this->assertEquals('02:00:00', $step->getDuration());
    }
}