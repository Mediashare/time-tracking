<?php
namespace Mediashare\Marathon\Service;

use Mediashare\Marathon\Collection\TimerCollection;
use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Timer;
use Mediashare\Marathon\Exception\FileNotFoundException;
use Mediashare\Marathon\Exception\JsonDecodeException;
use Mediashare\Marathon\Exception\TimerNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class TimerService {
    private SerializerService $serializerService;
    private Filesystem $filesystem;
    private StepService $stepService;
    private Timer $timer;

    public function __construct(
        private Config $config,
        bool $createItIfNotExist = true
    ) {
        $this->serializerService = new SerializerService();
        $this->filesystem = new Filesystem();

        $this->stepService = new StepService();

        $this->timer = $this->getTimer($createItIfNotExist);
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTimers(): TimerCollection {
        $timerCollection = new TimerCollection();
        foreach (glob($this->config->getTimerDirectory() . '/*') as $filepath):
            $timerCollection->add($this->serializerService->read($filepath, Timer::class));
        endforeach;

        return $timerCollection;
    }

    /**
     * @throws TimerNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTimer(bool $createItIfNotExist = true): Timer {
        $timerExist = $this->filesystem->exists($filepath = $this->getTimerFilepath());
        if (!$timerExist && $createItIfNotExist):
            return $this->timer = $this->createTimer();
        elseif (!$timerExist):
            throw new TimerNotFoundException();
        endif;

        return $this->timer = $this->serializerService->read($filepath, Timer::class);
    }

    public function createTimer(array $data = []): Timer {
        /** @var Timer $timer */
        $timer = $this->serializerService->arrayToEntity($data, Timer::class);

        if (!$timer->getId()):
            $timer->setId($this->config->getTimerId() ?? (new \DateTime())->format('YmdHis'));
        endif;

        if ($timer->isRun() && !$timer->getSteps()?->last()?->getEndDate()):
            $timer->addStep($this->stepService->createStep());
        endif;

        $this->serializerService->writeTimer($this->getTimerFilepath(), $timer);

        return $timer;
    }

    public function startTimer(
        string|false $name = false,
        string|false $duration = false,
    ): Timer {
        $timer = $this->timer->setRun(true);
        $timer->setName($name !== false ? $name : $timer->getName());

        if ($duration):
            $firstStep = $timer->getSteps()->first();
            $timer->getSteps()->clear();
            $timer->addStep($this
                ->stepService
                ->createStepWithCustomDuration(
                    $duration,
                    $firstStep?->getStartDate(),
                )
            );
        endif;

        if (!$timer->getStartDate() || !($lastStep = $timer->getSteps()?->last()) || $lastStep->getEndDate()):
            $timer
                ->addStep(
                    $this->stepService->createStep()
                );
        endif;

        return $timer;
    }

    public function stopTimer(): Timer {
        $timer = $this->timer
            ->setRun(false);

        if (($lastStep = $timer->getSteps()?->last()) && !$lastStep->getEndDate()):
            $timer
                ->getSteps()
                ->offsetSet(
                    $timer->getSteps()->getKey($lastStep),
                    $lastStep->setEndDate((new \DateTime())->getTimestamp()),
                );
        endif;

        return $timer;
    }

    public function archiveTimer(): Timer {
        return $this
            ->stopTimer()
            ->setArchived(true);
    }

    public function removeTimer(): self {
        $this->filesystem
            ->remove($this->getTimerFilepath())
        ;

        return $this;
    }

    /**
     * @throws TimerNotFoundException
     */
    public function getTimerFilepath(): string {
        if (!$this->config->getTimerId()):
            throw new TimerNotFoundException();
        endif;

        return $this->config->getTimerDirectory().DIRECTORY_SEPARATOR. $this->config->getTimerId().'.json';
    }
}