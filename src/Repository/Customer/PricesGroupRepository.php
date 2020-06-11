<?php

namespace App\Repository\Customer;

use App\Entity\Customer\PricesGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PricesGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method PricesGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method PricesGroup[]    findAll()
 * @method PricesGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricesGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricesGroup::class);
    }

    // /**
    //  * @return PriceGroup[] Returns an array of PriceGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PriceGroup
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
