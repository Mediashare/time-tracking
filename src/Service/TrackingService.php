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

    public function __construct(
        private Config $config,
    ) {
        $this->serializerService = new SerializerService();
        $this->filesystem = new Filesystem();
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
            return $this->createTracking();
        elseif (!$trackingExist):
            throw new TrackingNotFoundException();
        endif;

        return $this->serializerService->read($filepath, Tracking::class);
    }

    public function createTracking(array $data = []): Tracking {
        if (!array_keys($data, 'id')):
            $data = array_merge($data, [
                'id' => $this->config->getTrackingId()
                    ?? (new \DateTime())->format('YmdHis')
            ]);
        endif;

        /** @var Tracking $tracking */
        $tracking = $this->serializerService->arrayToEntity($data, Tracking::class);

        if ($tracking->isRun() && !$tracking->getSteps()?->last()?->getEndDate()):
            $stepService = new StepService($tracking);
            $tracking->addStep($stepService->createStep());
        endif;

        $this->serializerService->writeTracking($this->getTrackingFilepath(), $tracking);

        return $tracking;
    }

    public function startTracking(
        string|false $name = false,
        string|null $duration = null,
    ): Tracking {
        $tracking = $this->getTracking()->setRun(true);
        $tracking->setName($name !== false ? $name : $tracking->getName());

        if (!$tracking->getStartDate() || !($lastStep = $tracking->getSteps()?->last()) || $lastStep->getEndDate()):
            $stepService = new StepService();
            $tracking
                ->addStep(
                    $duration
                        ? $stepService->createStepWithCustomDuration($duration)
                        : $stepService->createStep()
                );
        endif;

        return $tracking;
    }

    public function stopTracking(bool $createItIfNotExist = true): Tracking {
        $tracking = $this->getTracking($createItIfNotExist);
        $tracking->setRun(false);

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

    public function archiveTracking(bool $stop = false): Tracking {
        $stop ? $this->stopTracking(createItIfNotExist: false) : null;
        return $this
            ->getTracking()
            ->setArchived(true);
    }

    public function removeTracking(): self {
        $this->filesystem
            ->remove($this->getTrackingFilepath())
        ;

        return $this;
    }

    public function getTrackingFilepath(): string {
        if (!$this->config->getTrackingId()):
            throw new TrackingNotFoundException();
        endif;

        return $this->config->getTrackingDirectory().DIRECTORY_SEPARATOR. $this->config->getTrackingId().'.json';
    }
}