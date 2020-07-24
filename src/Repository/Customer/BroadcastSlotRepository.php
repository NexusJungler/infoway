<?php

namespace App\Repository\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BroadcastSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method BroadcastSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method BroadcastSlot[]    findAll()
 * @method BroadcastSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BroadcastSlotRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BroadcastSlot::class);
    }

    // /**
    //  * @return BroadcastSlot[] Returns an array of BroadcastSlot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BroadcastSlot
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
