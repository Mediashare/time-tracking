<?php

namespace Mediashare\TimeTracking\Collection;

use Mediashare\TimeTracking\Entity\Step;
use Ramsey\Collection\AbstractCollection;

class StepCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Step::class;
    }

    public function last(): Step|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    public function first(): Step|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }

    public function getKey(Step $step): mixed {
        return array_search($step, $this->data);
    }
}