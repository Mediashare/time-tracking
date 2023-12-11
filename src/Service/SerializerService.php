<?php
namespace Mediashare\TimeTracking\Service;

use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Tracking;
use Mediashare\TimeTracking\Exception\FileNotFoundException;
use Mediashare\TimeTracking\Exception\JsonDecodeException;
use Mediashare\TimeTracking\Trait\ArrayToEntityTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerService {
    use ArrayToEntityTrait;

    private Serializer $serializer;
    private Filesystem $filesystem;

    public function __construct() {
        $this->serializer = new Serializer([
            new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
            new ArrayDenormalizer(),
        ], [new JsonEncoder()]);

        $this->filesystem = new Filesystem();
    }

    /**
     * Convert json file to Tracking Object
     */
    public function read(string $filepath, string $className): Tracking|Config {
        if (!$this->filesystem->exists($filepath)):
            throw new FileNotFoundException($filepath);
        endif;

        $content = file_get_contents($filepath);

        if (!($trackingArray = json_decode($content, true)) || json_last_error() !== JSON_ERROR_NONE):
            throw new JsonDecodeException($filepath);
        endif;

        return $this->arrayToEntity($trackingArray, $className);
    }

    /**
     * Write tracking file
     */
    public function writeTracking(string $filepath, Tracking $tracking): self {
        $this
            ->filesystem
            ->dumpFile(
                $filepath,
                $this->serializer->serialize($tracking, 'json')
            )
        ;

        return $this;
    }
}
