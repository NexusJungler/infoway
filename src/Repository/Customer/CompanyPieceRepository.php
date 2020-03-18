<?php

namespace App\Repository\Customer;

use App\Entity\Customer\CompanyPiece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CompanyPiece|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyPiece|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyPiece[]    findAll()
 * @method CompanyPiece[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyPieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyPiece::class);
    }

    // /**
    //  * @return CompanyPiece[] Returns an array of CompanyPiece objects
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
    public function findOneBySomeField($value): ?CompanyPiece
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
