<?php

namespace App\Repository\Admin;

use App\Entity\Admin\FfmpegTasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FfmpegTasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method FfmpegTasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method FfmpegTasks[]    findAll()
 * @method FfmpegTasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FfmpegTasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FfmpegTasks::class);
    }

    // /**
    //  * @return FfmpegTasks[] Returns an array of FfmpegTasks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FfmpegTasks
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
