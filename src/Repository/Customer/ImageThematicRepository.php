<?php

namespace App\Repository\Customer;

use App\Entity\Customer\ImageThematic;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ImageThematic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageThematic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageThematic[]    findAll()
 * @method ImageThematic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageThematicRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageThematic::class);
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
