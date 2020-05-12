<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ScreenDisplay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScreenDisplay|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScreenDisplay|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScreenDisplay[]    findAll()
 * @method ScreenDisplay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenDisplayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenDisplay::class);
    }

    // /**
    //  * @return ScreenDisplay[] Returns an array of ScreenDisplay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScreenDisplay
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
