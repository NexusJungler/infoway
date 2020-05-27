<?php

namespace App\Repository\Customer;

use App\Entity\Customer\PricesFactory;
use App\Repository\MainRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method PricesFactory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PricesFactory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PricesFactory[]    findAll()
 * @method PricesFactory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricesFactoryRepository extends ServiceEntityRepository
{

    use MainRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricesFactory::class);
    }

    // /**
    //  * @return PricesFactory[] Returns an array of PricesFactory objects
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
    public function findOneBySomeField($value): ?PricesFactory
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
