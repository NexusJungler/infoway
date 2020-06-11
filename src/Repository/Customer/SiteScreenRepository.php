<?php

namespace App\Repository\Customer;

use App\Entity\Customer\SiteScreen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiteScreen|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteScreen|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteScreen[]    findAll()
 * @method SiteScreen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteScreenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteScreen::class);
    }

    // /**
    //  * @return SiteScreen[] Returns an array of SiteScreen objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiteScreen
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
