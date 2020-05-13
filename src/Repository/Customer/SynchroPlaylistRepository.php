<?php

namespace App\Repository\Customer;

use App\Entity\Customer\SynchroPlaylist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method SynchroPlaylist|null find($id, $lockMode = null, $lockVersion = null)
 * @method SynchroPlaylist|null findOneBy(array $criteria, array $orderBy = null)
 * @method SynchroPlaylist[]    findAll()
 * @method SynchroPlaylist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynchroPlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SynchroPlaylist::class);
    }

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    // /**
    //  * @return SynchroPlaylist[] Returns an array of SynchroPlaylist objects
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
    public function findOneBySomeField($value): ?SynchroPlaylist
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
