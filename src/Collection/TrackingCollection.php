<?php

namespace Mediashare\TimeTracking\Collection;

use Ramsey\Collection\AbstractCollection;
use Mediashare\TimeTracking\Entity\Tracking;

class TrackingCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Tracking::class;
    }

    public function last(): Tracking|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    public function first(): Tracking|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }
}