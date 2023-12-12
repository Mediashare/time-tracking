<?php
namespace Mediashare\Marathon\Service;

use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Timer;
use Mediashare\Marathon\Exception\FileNotFoundException;
use Mediashare\Marathon\Exception\JsonDecodeException;
use Mediashare\Marathon\Trait\ArrayToEntityTrait;
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
     * Convert json file to Entity object
     * @throws FileNotFoundException
     * @throws JsonDecodeException
     */
    public function read(string $filepath, string $className): Timer|Config {
        if (!$this->filesystem->exists($filepath)):
            throw new FileNotFoundException($filepath);
        endif;

        $content = file_get_contents($filepath);

        if (!($timerArray = json_decode($content, true)) || json_last_error() !== JSON_ERROR_NONE):
            throw new JsonDecodeException($filepath);
        endif;

        return $this->arrayToEntity($timerArray, $className);
    }

    /**
     * Write timer file
     */
    public function writeTimer(string $filepath, Timer $timer): self {
        $this
            ->filesystem
            ->dumpFile(
                $filepath,
                $this->serializer->serialize($timer, 'json')
            )
        ;

        return $this;
    }
}
