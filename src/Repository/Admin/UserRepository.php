<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Action;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Permission;
use App\Entity\Admin\User;
use App\Entity\Customer\Role;
use App\Entity\Customer\Site;
use App\Service\ArraySearchRecursiveService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{


    private ArraySearchRecursiveService $__searchRecursiveService;
    private $_registry ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);

        $this->_registry = $registry;

        $this->__searchRecursiveService = new ArraySearchRecursiveService();
    }


    public function getUserWithSites(User $user){

        $userSites = $user->getSitesIds() ;

        $allSitesNeededFilteredByCustomersArray=[];

        foreach($userSites as $userSite){
            $siteCustomer = $userSite->getCustomer();
            $siteCustomerName = $siteCustomer->getName();

            if( !array_key_exists( $siteCustomerName , $allSitesNeededFilteredByCustomersArray ) )$allSitesNeededFilteredByCustomersArray[ $siteCustomerName ] = [] ;
            $allSitesNeededFilteredByCustomersArray[ $siteCustomerName ][] = $userSite->getSiteId();

        }

        $adminManager = $this->_registry->getManager('default');
        $customerRepo = $adminManager->getRepository(Customer::class) ;

        $allCustomers = $customerRepo->findBy(['name' => array_keys($allSitesNeededFilteredByCustomersArray)]);
        $allCustomersIndexedByName = [];

        foreach($allCustomers as $customer){
            $allCustomersIndexedByName[ $customer->getName() ] = $customer ;
        }
        foreach( $allSitesNeededFilteredByCustomersArray as $enseigne => $site ){

            if (!array_key_exists($enseigne,$allCustomersIndexedByName) || ! $allCustomersIndexedByName[$enseigne] instanceof Customer){
                continue ;
            }

            $enseigneEntity = $allCustomersIndexedByName[ $enseigne ];
            $adminManager = $this->_registry->getManager($enseigne);
            $siteRepo  = $adminManager->getRepository(Site::class );
            $userSites = $siteRepo->findBy(['id'=> $site]);

            foreach($userSites as $userSite){
                $enseigneEntity->addSite($userSite);
                $user->addSite($userSite,$enseigneEntity);
            }
        }

    }

    public function getUserWithRoles( User $user ){

        foreach($user->getUserRoles() as $userRole){

            $roleCustomer = $userRole->getCustomer();
            $siteCustomerName = $roleCustomer->getName();

            $userRoleId = $userRole->getRoleId();


            $currentEm = $this->_registry->getManager($siteCustomerName);

            $roleRepo  = $currentEm->getRepository( Role::class );
            $currentRole = $roleRepo->findOneById( $userRoleId );

            $user->addRole($currentRole,$roleCustomer);
        }

    }

    public function getUserWithSitesById($id){
        $user = $this->findOneById($id);

        $userSites = $user->getSitesIds() ;

        $allSitesNeededFilteredByCustomersArray=[];

        foreach($userSites as $userSite){
            $siteCustomer = $userSite->getCustomer();
            $siteCustomerName = $siteCustomer->getName();

            if( !array_key_exists( $siteCustomerName , $allSitesNeededFilteredByCustomersArray ) )$allSitesNeededFilteredByCustomersArray[ $siteCustomerName ] = [] ;
            $allSitesNeededFilteredByCustomersArray[ $siteCustomerName ][] = $userSite->getSiteId();

        }

        $adminManager = $this->_registry->getManager('default');
        $customerRepo = $adminManager->getRepository(Customer::class) ;

        $allCustomers = $customerRepo->findBy(['name' => array_keys($allSitesNeededFilteredByCustomersArray)]);
        $allCustomersIndexedByName = [];

        foreach($allCustomers as $customer){
            $allCustomersIndexedByName[ $customer->getName() ] = $customer ;
        }
        foreach( $allSitesNeededFilteredByCustomersArray as $enseigne => $site ){

            if (!array_key_exists($enseigne,$allCustomersIndexedByName) || ! $allCustomersIndexedByName[$enseigne] instanceof Customer){
                continue ;
            }

            $enseigneEntity = $allCustomersIndexedByName[ $enseigne ];
            $adminManager = $this->_registry->getManager($enseigne);
            $siteRepo  = $adminManager->getRepository(Site::class );
            $userSites = $siteRepo->findBy(['id'=> $site]);

            foreach($userSites as $userSite){

                $userSite->setCustomer($enseigneEntity);
                $user->addSite($userSite);
                dd($user);
            }
        }

    }

    public function setEntityManager(ObjectManager $entityManager)
    {
        $this->_em = $entityManager;

        return $this;
    }

    public function getUserPermissions(User $user, bool $onlyIds = false): array
    {
        return $this->reformatPermissions($user->getPermissions()->getValues(), $onlyIds);
    }


    public function getUserRolePermissions(User $user, bool $onlyIds = false): array
    {
        return $this->reformatPermissions($user->getRole()->getPermissions()->getValues(), $onlyIds);
    }


    /**
     * Reformat permissions array to :
     * [
     *      Feature.name => [ 'permissions' => [ [ 'id' => permission.id, 'name' => permission.name] ], ... ]
     * ]
     *
     * @param array $permissions
     * @return array
     */
    private function reformatPermissions(array $permissions, bool $onlyIds = false): array
    {

        $formattedPermissions = [];

        foreach ($permissions as $index => $permission)
        {

            $featurePosition = $this->__searchRecursiveService->search($permission->getFeature()->getName(), $formattedPermissions, null, false);

            if($featurePosition === false)
            {

                $data = [
                    'feature_id' => $permission->getFeature()->getId(),
                    'name' => $permission->getFeature()->getName(),
                    'permissions' => [
                        0 => [
                            'id' => $permission->getId(),
                            'name' => $permission->getName()
                        ]
                    ]
                ];

                $formattedPermissions[] = $data;

            }
            else
            {

                $formattedPermissions[$featurePosition]['permissions'][] = [
                    'id' => $permission->getId(),
                    'name' => $permission->getName()
                ];

            }


        }

        if($onlyIds)
            return $this->getPermissionsId($formattedPermissions);

        return $formattedPermissions;

    }


    private function getPermissionsId(array $formattedPermissions): array
    {

        $output['permissions'] = [];

        foreach ($formattedPermissions as $formattedPermission)
        {

            foreach ($formattedPermission['permissions'] as $permission)
            {
                $output['permissions'][] = $permission['id'];
            }

        }

        return $output;
    }



    /*private function reformatPermissions(array $permissions, bool $onlyIds = false): array
    {

        $formattedPermissions = [];

        foreach ($permissions as $index => $permission)
        {

            $featurePosition = $this->__searchRecursiveService->search($permission->getFeature()->getName(), $formattedPermissions, null, false);

            if($featurePosition === false)
            {

                if($onlyIds)
                {
                    $formattedPermissions['permissions_id'][] = $permission->getId();
                }
                else
                {

                    $data = [
                        'feature_id' => $permission->getFeature()->getId(),
                        'name' => $permission->getFeature()->getName(),
                        'permissions_id' => [
                            0 => $permission->getId()
                        ],
                        'subjects' => [
                            [
                                'id' => $permission->getSubject()->getId(),
                                'name' => $permission->getSubject()->getName(),
                                'checkboxes' => $this->loadCheckboxesState($permission)
                            ]
                        ]
                    ];

                    $formattedPermissions[] = $data;
                }

            }
            else
            {

                $formattedPermissions[$featurePosition]['permissions_id'][] = $permission->getId();

                if(!$onlyIds)
                    $subjectPosition = $this->__searchRecursiveService->search($permission->getSubject()->getName(), $formattedPermissions[$featurePosition]['subjects'], null, false);

                if((!$onlyIds) AND isset($subjectPosition) AND $subjectPosition === false)
                {

                    $formattedPermissions[$featurePosition]['subjects'][] = [
                        'id' => $permission->getSubject()->getId(),
                        'name' => $permission->getSubject()->getName(),
                        'checkboxes' => $this->loadCheckboxesState($permission)
                    ];

                }
                else
                {
                    if((!$onlyIds) AND isset($subjectPosition)) {
                        $action = $this->getEntityManager()->getRepository(Action::class)->findOneByName($permission->getAction()->getName());
                        $formattedPermissions[$featurePosition]['subjects'][$subjectPosition]['checkboxes'][$permission->getAction()->getName()] = $this->permissionIsAssociateWithAction($permission, $action);
                    }
                }

            }


        }

        //dd($formattedPermissions);

        return $formattedPermissions;

    }*/


    /**
     * Build and return an array which contain boolean to say if for each action checkbox must be displayed ( if action can be apply on subject )
     * If checkbox don't need to be displayed, index will contain null
     * e.g : [ 'Accéder' => null, 'Afficher' => true ]
     *
     * @param Permission $permission
     * @return array
     */
    private function loadCheckboxesState(Permission $permission)
    {

        $actions = $this->getEntityManager()->getRepository(Action::class)->findAll();

        $permissionCheckboxesStates = [];

        foreach ($actions as $action)
        {
            $permissionCheckboxesStates[$action->getName()] = $this->permissionIsAssociateWithAction($permission, $action);
        }

        return $permissionCheckboxesStates;

    }


    private function permissionIsAssociateWithAction(Permission $permission, Action $action)
    {
        return $action->getPermissions()->contains($permission)  ? [ 'permission_id' => $permission->getId(), 'action' => $action->getId(), 'subject' => $permission->getSubject()->getId() ] : null;
    }


    private function removeArrayKeysExcept(array $input, array $exceptKeys = []): array
    {

        foreach ($input as $key => $value)
        {
            if(!in_array($key, $exceptKeys))
                unset($input[$key]);
        }

        return $input;

    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
