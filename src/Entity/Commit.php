<?php
namespace Mediashare\TimeTracking\Entity;

use Mediashare\TimeTracking\Collection\StepCollection;
use Mediashare\TimeTracking\Trait\EntityDateTimeTrait;
use Mediashare\TimeTracking\Trait\EntityDurationTrait;
use Mediashare\TimeTracking\Trait\EntityUnserializerTrait;

class Commit {
    use EntityDateTimeTrait;
    use EntityDurationTrait;
    use EntityUnserializerTrait;

    private string|null $id = null;
    private string $message = '';
    private string $duration = '00:00:00';
    private StepCollection $steps;

    public function __construct() {
        $this
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

    public function setMessage(string $message): self {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string|null {
        return $this->message;
    }

    public function setSteps(StepCollection $steps): self {
        $this->steps = $steps;

        return $this;
    }

    public function getSteps(): StepCollection {
        return $this->steps;
    }

    public function addStep(Step $step): self {
        if (!$this->getSteps()->contains($step)):
            $this->getSteps()->add($step);
        endif;

        return $this;
    }

    public function toRender(int $index = 0, int $totalSeconds = 0, string $dateTimeFormat = Config::DATETIME_FORMAT) {
        return [
            'index' => $index,
            'id' => $this->id,
            'message' => $this->message,
            'duration' => $this->getDuration(),
            'duration_total' => $this->getDuration(totalSeconds: $totalSeconds),
            'startDate' => $this->getStartDateFormated($dateTimeFormat),
            'endDate' => $this->getEndDateFormated($dateTimeFormat),
        ];
    }
}
