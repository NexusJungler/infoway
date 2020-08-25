<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ScreenPlaylistEntry;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScreenPlaylistEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScreenPlaylistEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScreenPlaylistEntry[]    findAll()
 * @method ScreenPlaylistEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenPlaylistEntryRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenPlaylistEntry::class);
    }

    // /**
    //  * @return ScreenPlaylistEntry[] Returns an array of ScreenPlaylistEntry objects
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
    public function findOneBySomeField($value): ?ScreenPlaylistEntry
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
