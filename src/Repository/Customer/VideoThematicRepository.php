<?php

namespace App\Repository\Customer;

use App\Entity\Customer\VideoThematic;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoThematic|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoThematic|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoThematic[]    findAll()
 * @method VideoThematic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoThematicRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoThematic::class);
    }

    // /**
    //  * @return Thematic[] Returns an array of Thematic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Thematic
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
