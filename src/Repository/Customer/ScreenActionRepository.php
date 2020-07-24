<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ScreenAction;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScreenAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScreenAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScreenAction[]    findAll()
 * @method ScreenAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenActionRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenAction::class);
    }

    // /**
    //  * @return ScreenAction[] Returns an array of ScreenAction objects
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
    public function findOneBySomeField($value): ?ScreenAction
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
