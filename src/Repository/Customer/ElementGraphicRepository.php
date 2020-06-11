<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ElementGraphic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ElementGraphic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementGraphic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementGraphic[]    findAll()
 * @method ElementGraphic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementGraphicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementGraphic::class);
    }

    // /**
    //  * @return ElementGraphic[] Returns an array of ElementGraphic objects
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
    public function findOneBySomeField($value): ?ElementGraphic
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
