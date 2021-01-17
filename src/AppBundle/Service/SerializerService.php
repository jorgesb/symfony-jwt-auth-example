<?php

namespace AppBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SerializerService
 */
class SerializerService
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SerializerService constructor.
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
                ? $dateTime->format(\DateTime::ISO8601)
                : '';
        };

        $fields = ['created', 'modified'];
        $normalizer->setCallbacks(array_fill_keys($fields, $callback));

        $this->serializer = new Serializer([$normalizer], $encoders);
    }

    /**
     * @param mixed  $object
     * @param string $format
     *
     * @return bool|float|int|string
     */
    public function serialize($object, $format = 'json')
    {
        return $this->serializer->serialize(
            $object,
            $format
        );
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function decode($data)
    {
        return $this->serializer->decode($data, 'json');
    }

    public function normalize($object)
    {
        return $this->serializer->normalize($object);
    }
}
