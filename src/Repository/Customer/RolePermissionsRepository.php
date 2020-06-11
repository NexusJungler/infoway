<?php

namespace App\Repository\Customer;

use App\Entity\Customer\RolePermissions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RolePermissions|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolePermissions|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolePermissions[]    findAll()
 * @method RolePermissions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolePermissionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermissions::class);
    }

    // /**
    //  * @return RolePermissions[] Returns an array of RolePermissions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RolePermissions
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
