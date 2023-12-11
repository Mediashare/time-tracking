<?php
namespace Mediashare\TimeTracking\Entity;

use Mediashare\TimeTracking\Trait\EntityDateTimeTrait;
use Mediashare\TimeTracking\Trait\EntityDurationTrait;
use Mediashare\TimeTracking\Trait\EntityUnserializerTrait;

class Step {
    use EntityDateTimeTrait;
    use EntityDurationTrait;
    use EntityUnserializerTrait;

    private string $startDate;
    private string|null $endDate = null;

    public function setStartDate(string $startDate): self {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDate(string|null $endDate = null): self {
        $this->endDate = $endDate;

        return $this;
    }
}
