<?php

// src/Form/DataTransformer/IssueToNumberTransformer.php
namespace App\Form\DataTransformer;

use App\Entity\Customer\LocalProgramming;
use App\Entity\Customer\Media;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MouldToLocalProgrammingTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager('kfc');
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Media|null $issue
     * @return LocalProgramming
     */
    public function transform($mould)
    {

        if (null === $mould) {
            return '';
        }

        $localProgramming = new LocalProgramming();
        return $localProgramming->generateLocalProgrammingFromMould( $mould );
    }


    public function reverseTransform( $mould )
    {
// no issue number? It's optional, so that's ok
        if (! $mould ) {
            return;
        }

        $localProgramming = new LocalProgramming();
        $localProgramming->setMould( $mould );
//        $mould = $this->entityManager
//            ->getRepository(Media::class)
//// query for the issue with this id
//            ->find( $mould->getMould()->getId())
        ;

        if (null === $localProgramming) {
// causes a validation error
// this message is not shown to the user
// see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $mould
            ));
        }

        return $localProgramming;
    }
}