<?php

namespace App\Repository\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\Site;
use App\Repository\Admin\UserSitesRepository;
use Cassandra\Custom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findAll()
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    private UserSitesRepository $userSiteRepo ;

    public function __construct(ManagerRegistry $registry, UserSitesRepository $userSiteRepo)
    {

        $this->userSiteRepo = $userSiteRepo ;
        parent::__construct($registry, Site::class);
    }

    public function getSitesByUserAndCustomer(User $user, Customer $customer){

        $userSiteEntriesForCustomer = $this->userSiteRepo->getSitesByUserAndCustomer($user, $customer) ;

        $siteIdsInCustomer = array_map(function(UserSites $siteEntry){
            return $siteEntry->getSiteId() ;
        }, $userSiteEntriesForCustomer) ;

        return $this->findBy(['id' => $siteIdsInCustomer]) ;

    }

    public function getSitesWhereIdNotIn(array $ids){

        return  $this->createQueryBuilder('s')
            ->where('s.id NOT IN ( :ids )')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllSitesWhereCriterionNotAppear(Criterion $criterion){

        $criterionSitesIds = $criterion->getSites()->filter(function(Site $site){
            return $site->getId() ;
        });

        return $criterionSitesIds->count() <1 ? $this->findAll() : $this->getSitesWhereIdNotIn($criterionSitesIds->getValues() );
    }

    public function getTimeZoneByTimeZoneId(){

    }
    // /**
    //  * @return Site[] Returns an array of Site objects
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
    public function findOneBySomeField($value): ?Site
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
