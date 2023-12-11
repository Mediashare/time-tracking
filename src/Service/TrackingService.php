<?php
namespace Mediashare\TimeTracking\Service;

use Mediashare\TimeTracking\Collection\TrackingCollection;
use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Tracking;
use Mediashare\TimeTracking\Exception\FileNotFoundException;
use Mediashare\TimeTracking\Exception\JsonDecodeException;
use Mediashare\TimeTracking\Exception\TrackingNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class TrackingService {
    private SerializerService $serializerService;
    private Filesystem $filesystem;
    private StepService $stepService;
    private Tracking $tracking;

    public function __construct(
        private Config $config,
        bool $createItIfNotExist = true
    ) {
        $this->serializerService = new SerializerService();
        $this->filesystem = new Filesystem();

        $this->stepService = new StepService();

        $this->tracking = $this->getTracking($createItIfNotExist);
    }

    /**
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTrackings(): TrackingCollection {
        $trackingCollection = new TrackingCollection();
        foreach (glob($this->config->getTrackingDirectory() . '/*') as $filepath):
            $trackingCollection->add($this->serializerService->read($filepath, Tracking::class));
        endforeach;

        return $trackingCollection;
    }

    /**
     * @throws TrackingNotFoundException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     */
    public function getTracking(bool $createItIfNotExist = true): Tracking {
        $trackingExist = $this->filesystem->exists($filepath = $this->getTrackingFilepath());
        if (!$trackingExist && $createItIfNotExist):
            return $this->tracking = $this->createTracking();
        elseif (!$trackingExist):
            throw new TrackingNotFoundException();
        endif;

        return $this->tracking = $this->serializerService->read($filepath, Tracking::class);
    }

    public function createTracking(array $data = []): Tracking {
        /** @var Tracking $tracking */
        $tracking = $this->serializerService->arrayToEntity($data, Tracking::class);

        if (!$tracking->getId()):
            $tracking->setId($this->config->getTrackingId() ?? (new \DateTime())->format('YmdHis'));
        endif;

        if ($tracking->isRun() && !$tracking->getSteps()?->last()?->getEndDate()):
            $tracking->addStep($this->stepService->createStep());
        endif;

        $this->serializerService->writeTracking($this->getTrackingFilepath(), $tracking);

        return $tracking;
    }

    public function startTracking(
        string|false $name = false,
        string|false $duration = false,
    ): Tracking {
        $tracking = $this->tracking->setRun(true);
        $tracking->setName($name !== false ? $name : $tracking->getName());

        if ($duration):
            $firstStep = $tracking->getSteps()->first();
            $tracking->getSteps()->clear();
            $tracking->addStep($this
                ->stepService
                ->createStepWithCustomDuration(
                    $duration,
                    $firstStep?->getStartDate(),
                )
            );
        endif;

        if (!$tracking->getStartDate() || !($lastStep = $tracking->getSteps()?->last()) || $lastStep->getEndDate()):
            $tracking
                ->addStep(
                    $this->stepService->createStep()
                );
        endif;

        return $tracking;
    }

    public function stopTracking(): Tracking {
        $tracking = $this->tracking
            ->setRun(false);

        if (($lastStep = $tracking->getSteps()?->last()) && !$lastStep->getEndDate()):
            $tracking
                ->getSteps()
                ->offsetSet(
                    $tracking->getSteps()->getKey($lastStep),
                    $lastStep->setEndDate((new \DateTime())->getTimestamp()),
                );
        endif;

        return $tracking;
    }

    public function archiveTracking(): Tracking {
        return $this
            ->stopTracking()
            ->setArchived(true);
    }

    public function removeTracking(): self {
        $this->filesystem
            ->remove($this->getTrackingFilepath())
        ;

        return $this;
    }

    /**
     * @throws TrackingNotFoundException
     */
    public function getTrackingFilepath(): string {
        if (!$this->config->getTrackingId()):
            throw new TrackingNotFoundException();
        endif;

        return $this->config->getTrackingDirectory().DIRECTORY_SEPARATOR. $this->config->getTrackingId().'.json';
    }
}