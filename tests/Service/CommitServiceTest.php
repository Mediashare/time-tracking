<?php

namespace Mediashare\Marathon\Tests\Service;

use Mediashare\Marathon\Collection\CommitCollection;
use Mediashare\Marathon\Collection\StepCollection;
use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Tests\AbstractTestCase;
use Mediashare\Marathon\Service\CommitService;
use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Timer;
use Mediashare\Marathon\Exception\CommitNotFoundException;

class CommitServiceTest extends AbstractTestCase {
    private CommitService $commitService;

    protected function setUp(): void
    {
        $config = new Config(
            timerDirectory: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'marathon' . DIRECTORY_SEPARATOR . 'timers',
            timerId: (new \DateTime())->format('YmdHis')
        );

        $this->commitService = new CommitService($config);
    }

    public function testCreateCommit(): void {
        $timer = $this->commitService->createCommit('Test Commit', '+2 hours');

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertCount(1, $commits = $timer->getCommits());

        $this->assertInstanceOf(CommitCollection::class, $commits);
        $this->assertInstanceOf(Commit::class, $commit = $commits->first());

        $this->assertInstanceOf(StepCollection::class, $steps = $commit->getSteps());
        $this->assertInstanceOf(Step::class, $lastStep = $steps->first());
        $this->assertEquals("02:00:00", $lastStep->getDuration());

        $this->assertCount(1, $steps = $timer->getSteps());
        $this->assertInstanceOf(StepCollection::class, $steps);
        $this->assertInstanceOf(Step::class, $steps->first());
    }

    public function testEditCommit(): void {
        $timer = $this->commitService->createCommit('Original Commit', '+1 hour');
        $originalCommitId = $timer->getCommits()->first()->getId();

        $timer = $this->commitService->editCommit($originalCommitId, 'Updated Commit', '+30 minutes');

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertCount(1, $timer->getCommits());
        $this->assertCount(1, $timer->getSteps());

        $editedCommit = $timer->getCommits()->first();
        $this->assertEquals('Updated Commit', $editedCommit->getMessage());
    }

    public function testRemoveCommit(): void {
        $timer = $this->commitService->createCommit('To Be Removed Commit', '+3 hours');
        $toBeRemovedCommitId = $timer->getCommits()->first()->getId();

        $timer = $this->commitService->removeCommit($toBeRemovedCommitId);

        $this->assertInstanceOf(Timer::class, $timer);
        $this->assertCount(0, $timer->getCommits());
        $this->assertCount(1, $timer->getSteps());
    }

    public function testRemoveNonexistentCommit(): void {
        $this->expectException(CommitNotFoundException::class);

        $this->commitService->removeCommit('NonexistentCommitId');
    }
}