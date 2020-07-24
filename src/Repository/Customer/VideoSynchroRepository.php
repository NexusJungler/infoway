<?php

namespace App\Repository\Customer;

use App\Entity\Customer\VideoSynchro;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoSynchro|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoSynchro|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoSynchro[]    findAll()
 * @method VideoSynchro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoSynchroRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoSynchro::class);
    }

    // /**
    //  * @return VideoSynchro[] Returns an array of VideoSynchro objects
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
    public function findOneBySomeField($value): ?VideoSynchro
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
