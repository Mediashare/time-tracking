<?php

namespace Mediashare\TimeTracking\Tests\Service;

use Mediashare\TimeTracking\Collection\CommitCollection;
use Mediashare\TimeTracking\Collection\StepCollection;
use Mediashare\TimeTracking\Entity\Commit;
use Mediashare\TimeTracking\Entity\Step;
use PHPUnit\Framework\TestCase;
use Mediashare\TimeTracking\Service\CommitService;
use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Tracking;
use Mediashare\TimeTracking\Exception\CommitNotFoundException;

class CommitServiceTest extends TestCase
{
    private CommitService $commitService;

    protected function setUp(): void
    {
        $config = new Config(
            trackingDirectory: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'time-tracking',
            trackingId: (new \DateTime())->format('YmdHis')
        );

        $this->commitService = new CommitService($config);
    }

    public function testCreateCommit(): void {
        $tracking = $this->commitService->createCommit('Test Commit', '+2 hours');

        $this->assertInstanceOf(Tracking::class, $tracking);
        $this->assertCount(1, $commits = $tracking->getCommits());

        $this->assertInstanceOf(CommitCollection::class, $commits);
        $this->assertInstanceOf(Commit::class, $commit = $commits->first());

        $this->assertInstanceOf(StepCollection::class, $steps = $commit->getSteps());
        $this->assertInstanceOf(Step::class, $lastStep = $steps->first());
        $this->assertEquals("02:00:00", $lastStep->getDuration());

        $this->assertCount(1, $steps = $tracking->getSteps());
        $this->assertInstanceOf(StepCollection::class, $steps);
        $this->assertInstanceOf(Step::class, $steps->first());
    }

    public function testEditCommit(): void {
        $tracking = $this->commitService->createCommit('Original Commit', '+1 hour');
        $originalCommitId = $tracking->getCommits()->first()->getId();

        $tracking = $this->commitService->editCommit($originalCommitId, 'Updated Commit', '+30 minutes');

        $this->assertInstanceOf(Tracking::class, $tracking);
        $this->assertCount(1, $tracking->getCommits());
        $this->assertCount(1, $tracking->getSteps());

        $editedCommit = $tracking->getCommits()->first();
        $this->assertEquals('Updated Commit', $editedCommit->getMessage());
    }

    public function testRemoveCommit(): void {
        $tracking = $this->commitService->createCommit('To Be Removed Commit', '+3 hours');
        $toBeRemovedCommitId = $tracking->getCommits()->first()->getId();

        $tracking = $this->commitService->removeCommit($toBeRemovedCommitId);

        $this->assertInstanceOf(Tracking::class, $tracking);
        $this->assertCount(0, $tracking->getCommits());
        $this->assertCount(1, $tracking->getSteps());
    }

    public function testRemoveNonexistentCommit(): void {
        $this->expectException(CommitNotFoundException::class);

        $this->commitService->removeCommit('NonexistentCommitId');
    }
}