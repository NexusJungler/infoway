<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Devise;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Devise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devise[]    findAll()
 * @method Devise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviseRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devise::class);
    }

    // /**
    //  * @return Devise[] Returns an array of Devise objects
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
    public function findOneBySomeField($value): ?Devise
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
