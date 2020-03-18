<?php


namespace App\Normalizer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ApiNormalizer implements ContextAwareNormalizerInterface
{

    private $router;
    private static $normalizer;
    public const IGNORED_ATTRIBUTES = "ignored_attributes";

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        self::$normalizer = $normalizer;
    }

    static public function FILTER_ATTRIBUTES($filter) {
        foreach (self::$normalizer as $i => $property) {
            if(is_array($property)) {
                foreach ($property as $j => $value) {
                    if(strpos($j, $filter) !== false) {
                        unset(self::$normalizer[$i][$j]);
                    }
                }
            }
        }
        return self::$normalizer;
    }


    public function supportsNormalization($data, $format = null, array $context = [])
    {

        $data = $this->normalizer->normalize($data, $format, $context);



        return $data;

    }


    public function normalize($object, $format = null, array $context = [])
    {

    }
}