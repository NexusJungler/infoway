<?php

namespace App\Repository\Customer;

use App\Entity\Customer\PriceType;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceType[]    findAll()
 * @method PriceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricesTypeRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceType::class);
    }

    // /**
    //  * @return PriceType[] Returns an array of PriceType objects
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
    public function findOneBySomeField($value): ?PriceType
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
