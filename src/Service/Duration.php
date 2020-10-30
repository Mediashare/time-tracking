<?php
namespace Mediashare\Service;
use Mediashare\Service\Step;
class Duration {
    public $steps = [];
    public $seconds = 0;
    public $duration = '00:00:00';
    
    public function addStep(Step $step): self {
        $this->steps[] = $step;
        return $this;
    }

    // Convert seconds to HH:ii:ss
    public function getDuration(): string {
        $seconds = $this->getSeconds();
        $this->duration = sprintf('%02d:%02d:%02d', ($seconds/3600),($seconds/60%60), $seconds%60);
        return $this->duration;
    }

    public function getSeconds(): int {
        $seconds = 0;
        foreach ($this->steps ?? [] as $step):
            $parser = explode(':', $step->getDuration());
            $seconds += (($parser[0] ?? 0) * 60 * 60) + (($parser[1] ?? 0) * 60) + $parser[2] ?? 0;
        endforeach;
        $this->seconds = $seconds;
        return $this->seconds;
    }
}
