<?php

namespace App\Repository\Customer;

use App\Entity\Customer\DisplayMould;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DisplayMould|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisplayMould|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisplayMould[]    findAll()
 * @method DisplayMould[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisplayMouldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisplayMould::class);
    }

    // /**
    //  * @return DisplaySetting[] Returns an array of DisplaySetting objects
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
    public function findOneBySomeField($value): ?DisplaySetting
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
