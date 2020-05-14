<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as SymfonyDateTimeNormalizer;


class IgnoreNotAllowedNulledAttributeNormalizer extends SymfonyDateTimeNormalizer
{



    private $getTypeMethodReflection ;
    private $localObjectClassResolver;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        $this->localObjectClassResolver = $objectClassResolver ;
        $this->getTypeMethodReflection =  new \ReflectionMethod('Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer','getTypes') ;
        $this->getTypeMethodReflection->setAccessible(true);
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

    }


    private function getTypes(string $currentClass, string $attribute){
        return $this->getTypeMethodReflection->invoke($this, $currentClass,  $attribute) ;
    }

    protected function ignoreNulledAttributes($data , $type , $context ,$format)
    {


        $normalizedData = parent::prepareForDenormalization($data) ;
        $allowedAttributes = $this->getAllowedAttributes($type, $context, true);
        $reflectionClass = new \ReflectionClass($type);


//        $p->setAccessible(true); //

        $object = $this->instantiateObject($normalizedData, $type, $context, $reflectionClass, $allowedAttributes, $format);


        $resolvedClass = $this->localObjectClassResolver ? ($this->localObjectClassResolver)($object) : \get_class($object);

        foreach ($normalizedData as $attribute => $value) {

            if (null === $types = $this->getTypes($resolvedClass, $attribute)) {

                return $context;
            }

            foreach ($types as $type) {

                if (null === $value && !$type->isNullable()) {


                    $context['ignored_attributes'] = $context['ignored_attributes'] ?? [];
                    if (!in_array($attribute, $context['ignored_attributes'])) $context['ignored_attributes'][] = $attribute;
                }
            }

        }


        return $context ;

    }

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        try {

            $newContext = $this->ignoreNulledAttributes($data , $class , $context ,$format);



            return parent::denormalize($data, $class, $format, $newContext);
        } catch (NotNormalizableValueException $e) {
            return $data;
        }
    }
}