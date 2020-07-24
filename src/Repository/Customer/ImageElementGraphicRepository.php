<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ImageElementGraphic;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ImageElementGraphic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageElementGraphic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageElementGraphic[]    findAll()
 * @method ImageElementGraphic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageElementGraphicRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageElementGraphic::class);
    }

    // /**
    //  * @return ImageElementGraphic[] Returns an array of ImageElementGraphic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageElementGraphic
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
