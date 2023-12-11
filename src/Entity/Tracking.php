<?php
namespace Mediashare\TimeTracking\Entity;

use Mediashare\TimeTracking\Collection\CommitCollection;
use Mediashare\TimeTracking\Collection\StepCollection;
use Mediashare\TimeTracking\Trait\EntityDateTimeTrait;
use Mediashare\TimeTracking\Trait\EntityDurationTrait;
use Mediashare\TimeTracking\Trait\EntityUnserializerTrait;

class Tracking {
    use EntityDateTimeTrait;
    use EntityDurationTrait;
    use EntityUnserializerTrait;

    private string|null $id = null;
    private string $name = '';
    private bool $run = true;
    private bool $archived = false;

    /** @var CommitCollection<Commit> */
    private CommitCollection $commits;
    /** @var StepCollection<Step> */
    private StepCollection $steps;

    public function __construct() {
        $this
            ->setCommits(new CommitCollection())
            ->setSteps(new StepCollection())
        ;
    }

    public function setId(string $id): self {
        $this->id = $id;

        return $this;
    }

    public function getId(): string {
        return $this->id;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setRun(bool $run): self {
        $this->run = $run;

        return $this;
    }

    public function isRun(): bool {
        return $this->run;
    }

    public function setArchived(bool $archived): self {
        $this->archived = $archived;

        return $this;
    }

    public function isArchived(): bool {
        return $this->archived;
    }

    public function getStatus(): string {
        if ($this->isArchived()): return 'archived'; elseif ($this->run): return 'run'; else: return 'pause'; endif;
    }

    public function setCommits(CommitCollection $commits): self {
        $this->commits = $commits;

        return $this;
    }

    public function getCommits(): CommitCollection {
        return $this->commits;
    }

    public function addCommit(Commit $commit): self {
        if (!$this->getCommits()->contains($commit)):
            $this->getCommits()->add($commit);
        endif;

        return $this;
    }

    public function removeCommit(Commit $commit): self {
        if ($this->getCommits()->contains($commit)):
            $this->getCommits()->remove($commit);
        endif;

        return $this;
    }

    public function removeCommits(CommitCollection $commits): self {
        $commits
            ->map(fn (Commit $commit) => $this->removeCommit($commit));

        return $this;
    }

    /**
     * @param StepCollection<Step> $steps
     * @return $this
     */
    public function setSteps(StepCollection $steps): self {
        $this->steps = $steps;

        return $this;
    }

    /**
     * @return StepCollection<Step>
     */
    public function getSteps(): StepCollection {
        return $this->steps;
    }

    public function addStep(Step $step): self {
        if (!$this->getSteps()->contains($step)):
            $this->getSteps()->add($step);
        endif;

        return $this;
    }

    public function toRender(string $dateTimeFormat = Config::DATETIME_FORMAT): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'running' => \ucfirst($this->getStatus()),
            'commits' => $this->getCommits()->count(),
            'duration' => $this->getDuration(),
            'current_timer' => $this->getDuration(onlyNotCommited: true),
            'startDate' => $this->getStartDateFormated($dateTimeFormat),
            'endDate' => $this->getEndDateFormated($dateTimeFormat),
        ];
    }
}
