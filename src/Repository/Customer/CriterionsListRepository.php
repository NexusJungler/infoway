<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CriterionsList;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


class CriterionsListRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CriterionsList::class);
    }


    public function getAllsCriterionsListsFromDB()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult()
            ;
    }
    public function getAllsCriterionsListsNamesFromDB()
    {
        return array_map( function( CriterionsList $criterionsList ) {
            return $criterionsList->getName() ;
        }, $this->getAllsCriterionsListsFromDB() );
    }

    // /**
    //  * @return CriterionCategory[] Returns an array of CriterionCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CriterionCategory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
