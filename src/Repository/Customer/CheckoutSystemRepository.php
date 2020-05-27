<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CheckoutSystem;
use App\Repository\MainRepository;
use App\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method CheckoutSystem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckoutSystem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckoutSystem[]    findAll()
 * @method CheckoutSystem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckoutSystemRepository extends ServiceEntityRepository
{

    use MainRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckoutSystem::class);
    }

    // /**
    //  * @return CheckoutSystem[] Returns an array of CheckoutSystem objects
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
    public function findOneBySomeField($value): ?CheckoutSystem
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
