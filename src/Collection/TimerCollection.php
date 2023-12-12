<?php

namespace Mediashare\Marathon\Collection;

use Ramsey\Collection\AbstractCollection;
use Mediashare\Marathon\Entity\Timer;

class TimerCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Timer::class;
    }

    public function last(): Timer|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    public function first(): Timer|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }
}