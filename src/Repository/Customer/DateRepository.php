<?php

namespace App\Repository\Customer;

use App\Entity\Customer\date;
use App\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method date|null find($id, $lockMode = null, $lockVersion = null)
 * @method date|null findOneBy(array $criteria, array $orderBy = null)
 * @method date[]    findAll()
 * @method date[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, date::class);
    }

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    // /**
    //  * @return date[] Returns an array of date objects
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
    public function findOneBySomeField($value): ?date
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
