<?php

namespace App\Repository\Customer;

use App\Entity\Customer\DisplaySpace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DisplaySpace|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisplaySpace|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisplaySpace[]    findAll()
 * @method DisplaySpace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisplaySpaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisplaySpace::class);
    }

    // /**
    //  * @return DisplaySpace[] Returns an array of DisplaySpace objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DisplaySpace
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
