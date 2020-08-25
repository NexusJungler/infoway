<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Synchro;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Synchro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Synchro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Synchro[]    findAll()
 * @method Synchro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynchroRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Synchro::class);
    }

    public function findAllNames()
    {
        $names = [];

        foreach ($this->findAll() as $media)
        {
            $names[] = $media->getName();
        }

        return $names;
    }

    // /**
    //  * @return Synchro[] Returns an array of Synchro objects
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
    public function findOneBySomeField($value): ?Synchro
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
