<?php

namespace App\Repository\Customer;

use App\Entity\Customer\MainPrice;
use App\Repository\MainRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method MainPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainPrice[]    findAll()
 * @method MainPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainPriceRepository extends ServiceEntityRepository
{

    use MainRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainPrice::class);
    }

    // /**
    //  * @return MainPrice[] Returns an array of MainPrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MainPrice
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
