<?php

namespace App\Repository\Customer;

use App\Entity\Customer\SynchroElement;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SynchroElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method SynchroElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method SynchroElement[]    findAll()
 * @method SynchroElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynchroElementRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SynchroElement::class);
    }

    // /**
    //  * @return SynchroElement[] Returns an array of SynchroElement objects
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
    public function findOneBySomeField($value): ?SynchroElement
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
