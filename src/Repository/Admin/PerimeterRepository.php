<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Perimeter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Perimeter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Perimeter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Perimeter[]    findAll()
 * @method Perimeter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerimeterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perimeter::class);
    }

    // /**
    //  * @return Perimeter[] Returns an array of Perimeter objects
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
    public function findOneBySomeField($value): ?Perimeter
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
