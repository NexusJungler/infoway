<?php

namespace App\Repository\Customer;

use App\Entity\Customer\VideoElementGraphic;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoElementGraphic|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoElementGraphic|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoElementGraphic[]    findAll()
 * @method VideoElementGraphic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoElementGraphicRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoElementGraphic::class);
    }

    // /**
    //  * @return VideoElementGraphic[] Returns an array of VideoElementGraphic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoElementGraphic
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}