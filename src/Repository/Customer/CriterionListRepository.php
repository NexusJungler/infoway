<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CriterionCategory;
use App\Entity\Customer\CriterionList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CriterionCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CriterionCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CriterionCategory[]    findAll()
 * @method CriterionCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriterionListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CriterionList::class);
    }

    // /**
    //  * @return CriterionCategory[] Returns an array of CriterionCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CriterionCategory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
