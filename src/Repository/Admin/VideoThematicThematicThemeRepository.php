<?php

namespace App\Repository\Admin;

use App\Entity\Admin\VideoThematicThematicTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VideoThematicThematicTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoThematicThematicTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoThematicThematicTheme[]    findAll()
 * @method VideoThematicThematicTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoThematicThematicThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoThematicThematicTheme::class);
    }

    // /**
    //  * @return VideoThematicTheme[] Returns an array of VideoThematicTheme objects
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
    public function findOneBySomeField($value): ?VideoThematicTheme
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
