<?php

namespace Mediashare\Marathon\Trait;

use Mediashare\Marathon\Collection\CommitCollection;
use Mediashare\Marathon\Collection\StepCollection;
use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Step;
use Mediashare\Marathon\Entity\Timer;

trait EntityUnserializerTrait {
    use ArrayToEntityTrait;

    public function __unserialize(array $data): void {
        switch (self::class) {
            case Config::class:
                foreach ($data as $key => $value):
                    if (!is_array($value) && method_exists(Config::class, $method = 'set' . $key)):
                        $this->{$method}($value);
                    endif;
                endforeach;
                break;
            case Timer::class:
                foreach ($data as $key => $value):
                    if (!is_array($value) && method_exists(Timer::class, $method = 'set' . $key)):
                        $this->{$method}($value);
                    endif;
                endforeach;

                $this
                    ->setCommits(new CommitCollection(array_map(
                            fn (array $commit) => $this->arrayToEntity($commit, Commit::class),
                            $data['commits'] ?? [],
                        ))
                    )
                    ->setSteps(new StepCollection(array_map(
                            fn (array $step) => $this->arrayToEntity($step, Step::class),
                            $data['steps'] ?? [],
                        ))
                    );
                break;
            case Commit::class:
                foreach ($data as $key => $value):
                    if (!is_array($value) && method_exists(Commit::class, $method = 'set' . $key)):
                        $this->{$method}($value);
                    endif;
                endforeach;
                $this->setSteps(new StepCollection(array_map(
                        fn (array $step) => $this->arrayToEntity($step, Step::class),
                        $data['steps'] ?? [],
                    ))
                );
                break;
            case Step::class:
                foreach ($data as $key => $value):
                    if (!is_array($value) && method_exists(Step::class, $method = 'set' . $key)):
                        $this->{$method}($value);
                    endif;
                endforeach;
                break;
        }
    }
}