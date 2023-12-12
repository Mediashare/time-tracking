<?php

namespace Mediashare\Marathon\Trait;

use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Entity\Timer;

trait ArrayToEntityTrait {
    public function arrayToEntity(
        array $array,
        string $className
    ): Config|Timer|Commit|Step {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(
                serialize($array),
                ':'
            )
        ), [
            Config::class,
            Timer::class,
            Commit::class,
            Step::class,
        ]);
    }
}