<?php
namespace Mediashare\Marathon\Entity;

use Mediashare\Marathon\Trait\EntityDateTimeTrait;
use Mediashare\Marathon\Trait\EntityDurationTrait;
use Mediashare\Marathon\Trait\EntityUnserializerTrait;

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
