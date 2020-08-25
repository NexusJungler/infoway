<?php

namespace App\Repository\Customer;

use App\Entity\Customer\LocalProgramming;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LocalProgramming|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalProgramming|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalProgramming[]    findAll()
 * @method LocalProgramming[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalProgrammingRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalProgramming::class);
    }

    // /**
    //  * @return LocalProgramming[] Returns an array of LocalProgramming objects
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
    public function findOneBySomeField($value): ?LocalProgramming
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
