<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ExpectedChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method ExpectedChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpectedChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpectedChange[]    findAll()
 * @method ExpectedChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpectedChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpectedChange::class);
    }

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    // /**
    //  * @return ExpectedChange[] Returns an array of ExpectedChange objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpectedChange
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
