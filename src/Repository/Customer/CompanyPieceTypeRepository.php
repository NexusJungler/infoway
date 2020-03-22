<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CompanyPieceType;
use App\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method CompanyPieceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyPieceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyPieceType[]    findAll()
 * @method CompanyPieceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyPieceTypeRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyPieceType::class);
    }


    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    // /**
    //  * @return CompanyPieceType[] Returns an array of CompanyPieceType objects
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
    public function findOneBySomeField($value): ?CompanyPieceType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByLevel($value) {
        return $this->createQueryBuilder('s')
            ->andWhere('s.level > :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
}
