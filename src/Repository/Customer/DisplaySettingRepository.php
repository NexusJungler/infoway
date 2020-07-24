<?php

namespace App\Repository\Customer;

use App\Entity\Customer\DisplaySetting;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method DisplaySetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisplaySetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisplaySetting[]    findAll()
 * @method DisplaySetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisplaySettingRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisplaySetting::class);
    }

    // /**
    //  * @return DisplaySetting[] Returns an array of DisplaySetting objects
    //  */



    // /**
    //  * @return DisplaySetting[] Returns an array of DisplaySetting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DisplaySetting
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
