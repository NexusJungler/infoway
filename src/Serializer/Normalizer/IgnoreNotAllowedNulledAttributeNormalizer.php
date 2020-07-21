<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as SymfonyDateTimeNormalizer;
use Symfony\Component\Validator\Constraints\DateTime;


class IgnoreNotAllowedNulledAttributeNormalizer extends SymfonyDateTimeNormalizer
{

    private $getTypeMethodReflection ;
    private $localObjectClassResolver;
    private $contextPerEntity;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        $this->localObjectClassResolver = $objectClassResolver ;
        $this->getTypeMethodReflection =  new \ReflectionMethod('Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer','getTypes') ;
        $this->getTypeMethodReflection->setAccessible(true);
        $this->contextPerEntity = [] ;
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

    }


    private function getTypes(string $currentClass, string $attribute){
        return $this->getTypeMethodReflection->invoke($this, $currentClass,  $attribute) ;
    }

    protected function ignoreNulledAttributes($data , $class , $context ,$format)
    {


        $normalizedData = parent::prepareForDenormalization($data) ;
        $allowedAttributes = $this->getAllowedAttributes($class, $context, true);
        $reflectionClass = new \ReflectionClass($class);

        $object = $this->instantiateObject($normalizedData, $class, $context, $reflectionClass, $allowedAttributes, $format);


        $resolvedClass = $this->localObjectClassResolver ? ($this->localObjectClassResolver)($object) : \get_class($object);

        foreach ($normalizedData as $attribute => $value) {


//            dd($this->getTypes($resolvedClass, $attribute));
            if (null === $types = $this->getTypes($resolvedClass, $attribute)) {
//                $this->contextPerEntity[ $class ]['ignored_attributes'] = $this->contextPerEntity[ $class ]['ignored_attributes'] ?? [];
//                $this->contextPerEntity[ $class ]['ignored_attributes'][] = $attribute ;
                continue ;
            }

            foreach ($types as $type) {
                if($attribute == 'id' && $value === null )$normalizedData[$attribute] = 10;
                if (null === $value && !$type->isNullable()) {


                    $this->contextPerEntity[ $class ]['ignored_attributes'] = $this->contextPerEntity[ $class ]['ignored_attributes'] ?? [];
                    if (!in_array($attribute, $this->contextPerEntity[ $class ]['ignored_attributes']) && $attribute !== 'id') {
                        $this->contextPerEntity[ $class ]['ignored_attributes'][] = $attribute;
                    }
                }
            }

        }


        return $context ;

    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        return is_array( $data ) ?  array_filter($data, function ($value) {
            return null !== $value;
        }) : $data ;
    }


    public function denormalize($data, $class, $format = null, array $context = array())
    {

//            if( !array_key_exists( $class , $this->contextPerEntity ) ) {
//                $this->contextPerEntity[ $class ] = [] ;
//            }

//        dump($this->contextPerEntity);
        if( $class === 'DateTime' ){

            $timezone = $data['timezone']['name']  ?? false ;
            $timestamp = $data['timestamp'] ?? false ;

            if($timezone && $timestamp ){
//                    dump($class);
                $dateTime = new \DateTime();
                return $dateTime->setTimezone(new \DateTimeZone($timezone))->setTimestamp($timestamp);
            }
//            return new \DateTime( $data[0] );
        }

//        if(is_array($data) && array_key_exists('id',$data) && $data['id'] === null ){
//           $data['id'] = 3 ;
//        }

//        $this->ignoreNulledAttributes($data , $class , $this->contextPerEntity[ $class ] ,$format);






            return parent::denormalize( $data, $class, $format,$context );
    }
}