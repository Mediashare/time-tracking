<?php

namespace Mediashare\TimeTracking\Trait;

use Mediashare\TimeTracking\Entity\Commit;
use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Step;
use Mediashare\TimeTracking\Entity\Tracking;

trait ArrayToEntityTrait {
    public function arrayToEntity(
        array $array,
        string $className
    ): Config|Tracking|Commit|Step {
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
            Tracking::class,
            Commit::class,
            Step::class,
        ]);
    }
}