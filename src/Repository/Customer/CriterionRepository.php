<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Criterion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Criterion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Criterion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Criterion[]    findAll()
 * @method Criterion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriterionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Criterion::class);
    }

    public function getAnotherCriterionWithSameName( Criterion $criterion ) : ?Criterion{
        return  $this->createQueryBuilder('c')
            ->where('c.id !=  :id')
            ->andWhere('c.name = :name')
            ->setParameter('id', $criterion->getId() )
            ->setParameter('name' , $criterion->getName() )
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Cristerion[] Returns an array of Cristerion objects
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
    public function findOneBySomeField($value): ?Cristerion
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
