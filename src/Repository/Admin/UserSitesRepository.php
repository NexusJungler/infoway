<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Customer;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserSites|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSites|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSites[]    findAll()
 * @method UserSites[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSitesRepository extends ServiceEntityRepository
{

    private $registry ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSites::class);
        $this->registry = $registry ;
    }

    public function getSitesInCustomer(Customer $customer , array $siteIds) {
        $allManagers = $this->registry->getManagers() ;

        if( !isset( $allManagers[ $customer->getName() ] )  ) throw new \Error('invalid customer base') ;

        $currentManager = $allManagers[ $customer->getName() ] ;
        $allSitesReceived = $currentManager->getRepository(Site::class)->findById($siteIds) ;

        return ['customer' => $customer , 'sites' => $allSitesReceived] ;

    }
    // /**
    //  * @return UserSites[] Returns an array of UserSites objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserSites
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
