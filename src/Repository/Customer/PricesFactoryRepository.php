<?php

namespace App\Repository\Customer;

use App\Entity\Customer\PricesFactory;
use App\Repository\RepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method PricesFactory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PricesFactory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PricesFactory[]    findAll()
 * @method PricesFactory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricesFactoryRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

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
    public function findOneBySomeField($value): ?PricesFactory
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
