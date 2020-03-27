<?php

namespace App\Repository\Admin;

use App\Entity\Admin\UserSites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserSites|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSites|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSites[]    findAll()
 * @method UserSites[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSitesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSites::class);
    }

    // /**
    //  * @return UserSites[] Returns an array of UserSites objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserSites
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
