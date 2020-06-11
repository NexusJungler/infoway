<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    public function getSitesByUserAndCustomer(User $user, Customer $customer){
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->andWhere('u.customer = :customer')
            ->setParameter('user', $user->getId())
            ->setParameter('customer', $customer->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function getSitesByIdsAndCustomerName(Collection $sites , Customer $customer, User $user){

        $sitesEntities = $sites->filter( function( $site ) {
            return $site instanceof Site ;
        }) ;
        $sitesIds = $sitesEntities->map( function( $site ){
            return $site->getId() ;
        } ) ;

//        $siteIds->filter( function() )
        return $this->createQueryBuilder('u')
            ->andWhere('u.siteId IN (:siteIds)')
            ->andWhere('u.customer = :customer')
            ->andWhere('u.user = :user')
            ->setParameter('siteIds', $sitesIds->toArray())
            ->setParameter('customer',$customer)
            ->setParameter('user',$user)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getSitesIdsByIdsAndCustomerName(Collection $sites , Customer $customer, User $user){

    $siteEntries = $this->getSitesByIdsAndCustomerName($sites, $customer, $user ) ;

    return array_map( function( $siteEntry ){
        return $siteEntry->getSiteId() ;
    }, $siteEntries) ;

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
