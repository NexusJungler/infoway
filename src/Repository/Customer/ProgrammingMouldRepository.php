<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ProgrammingMould;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProgrammingMould|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammingMould|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammingMould[]    findAll()
 * @method ProgrammingMould[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammingMouldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammingMould::class);
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
