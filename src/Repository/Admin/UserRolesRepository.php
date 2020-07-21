<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Customer;
use App\Entity\Admin\UserRoles;
use App\Entity\Customer\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Admin\User;

/**
 * @method UserRoles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRoles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRoles[]    findAll()
 * @method UserRoles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRolesRepository extends ServiceEntityRepository
{
    private $registry ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRoles::class);
        $this->registry = $registry ;
    }

    public function getRolesInCustomer(Customer $customer , array $rolesIds) {
        $allManagers = $this->registry->getManagers() ;

        if( !isset( $allManagers[ $customer->getName() ] )  ) throw new \Error('invalid customer base') ;

            $currentManager = $allManagers[ $customer->getName() ] ;
            $allRolesReceived = $currentManager->getRepository(Role::class)->findById($rolesIds) ;

        return ['customer' => $customer , 'roles' => $allRolesReceived] ;

    }

    // /**
    //  * @return UserRoles[] Returns an array of UserRoles objects
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
    public function findOneBySomeField($value): ?UserRoles
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
