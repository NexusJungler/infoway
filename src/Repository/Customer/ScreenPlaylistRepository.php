<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ScreenPlaylist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScreenPlaylist|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScreenPlaylist|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScreenPlaylist[]    findAll()
 * @method ScreenPlaylist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenPlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenPlaylist::class);
    }

    // /**
    //  * @return ScreenPlaylist[] Returns an array of ScreenPlaylist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScreenPlaylist
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
