<?php

namespace App\Repository\Customer;

use App\Entity\Customer\NightProgramming;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NightProgramming|null find($id, $lockMode = null, $lockVersion = null)
 * @method NightProgramming|null findOneBy(array $criteria, array $orderBy = null)
 * @method NightProgramming[]    findAll()
 * @method NightProgramming[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NightProgrammingRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NightProgramming::class);
    }

    // /**
    //  * @return NightProgramming[] Returns an array of NightProgramming objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NightProgramming
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
