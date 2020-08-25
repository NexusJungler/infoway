<?php

namespace App\Repository\Admin;

use App\Entity\Admin\ThematicTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ThematicTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThematicTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThematicTheme[]    findAll()
 * @method ThematicTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThematicThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThematicTheme::class);
    }

    // /**
    //  * @return ThematicTheme[] Returns an array of ThematicTheme objects
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
    public function findOneBySomeField($value): ?ThematicTheme
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
