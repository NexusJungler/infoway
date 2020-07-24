<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CheckoutProduct;
use App\Repository\RepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method CheckoutProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckoutProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckoutProduct[]    findAll()
 * @method CheckoutProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckoutProductRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckoutProduct::class);
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
