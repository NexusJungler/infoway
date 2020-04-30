<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Action;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Permission;
use App\Entity\Admin\User;
use App\Entity\Admin\UserRoles;
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


    public function getUsersWithRoleBellowUserByCustomer(Customer $customer, User $user)
    {
        $customerEM = $this->_registry->getManager( $customer->getName() ) ;
        $customerRoleRepo = $customerEM->getRepository(Role::class) ;

        $allRolesAvailablesInCustomer = $customerRoleRepo->findAll() ;
        $allRolesAvailableInCustomerWithIdAsKey = [] ;

        foreach($allRolesAvailablesInCustomer as $roleAvailableInCustomer){

            $allRolesAvailableInCustomerWithIdAsKey[ $roleAvailableInCustomer->getId() ] = $roleAvailableInCustomer ;
        }

        $userRoleIdInCustomer = $user->getUserRoles()->filter(function(UserRoles $userRole) use ( $customer ) {

            return $userRole->getCustomer()->getName() === $customer->getName() ;
        }) ;

        if($userRoleIdInCustomer->count() <1 ) throw new \Error( 'Impossible to find User Role in Customer') ;
        $userRoleIdInCustomer = $userRoleIdInCustomer[ 0 ] ;

        $userRoleInCustomer = array_filter( $allRolesAvailablesInCustomer, function( Role $roleAvailableInCustomer ) use ( $userRoleIdInCustomer){

            return $roleAvailableInCustomer->getId() === $userRoleIdInCustomer->getRoleId() ;
        } ) ;

        if( count( $userRoleInCustomer ) <  1 ) throw new \Error('Impossible to get Role in Role List in Customer ') ;
        $userRoleInCustomer = $userRoleInCustomer[0] ;

        $allRoleWithLevelBellowUser = array_filter( $allRolesAvailablesInCustomer , function( $roleAvailableInCustomer ) use ( $userRoleInCustomer ) {

            return $roleAvailableInCustomer->getLevel() > $userRoleInCustomer->getLevel() ;
        }) ;


        $allRolesIdsWithLevelBellowUser = array_map( function( $roleWithLevelBellowUser ){

            return $roleWithLevelBellowUser->getId() ;
        } , $allRoleWithLevelBellowUser ) ;


        $usersWithRoleBellowInDb = $this->createQueryBuilder('u')
            ->leftJoin('u.userRoles', 'ur')
            ->andWhere('ur.roleId IN (:rolesId)' )
            ->andWhere('ur.customer = :customer')
            ->setParameter('rolesId', $allRolesIdsWithLevelBellowUser)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getResult() ;


        foreach($usersWithRoleBellowInDb as $userWithRoleBellowInDb){

            foreach( $userWithRoleBellowInDb->getUserRoles() as $userRoleEntry){

                if ( $userRoleEntry->getCustomer()->getId() === $customer->getId() ) {

                    if( isset( $allRolesAvailableInCustomerWithIdAsKey[$userRoleEntry->getRoleId()] ) ){

                        $rolePossessedByUser = $allRolesAvailableInCustomerWithIdAsKey[ $userRoleEntry->getRoleId() ] ;
                        $userWithRoleBellowInDb->addRole( $rolePossessedByUser , $customer ) ;
                    }
                }
            }

        }

        return $usersWithRoleBellowInDb ;
    }
    //Methode de recuperaton d un user present  recupere de la base admin avec ses sites recupere dans chaque base correspondante
    public function getUserWithSites(User $user){

        //Recuperation de tous les ids de chaque site appartenant à l'user importés  de la base admin
        $userSites = $user->getUserSites() ;


        //creation du tableau contenant les ids de l user rangés par enseigne (en clé de tableau )  afin d effectuer une seule connection par enseigne quand on va aller chercher les objets site auxquels les ids appartiennent dans leur base
        $allSitesNeededFilteredByCustomersArray=[];

        foreach($userSites as $userSite){
            $siteCustomer = $userSite->getCustomer();
            $siteCustomerName = $siteCustomer->getName();

            if( !array_key_exists( $siteCustomerName , $allSitesNeededFilteredByCustomersArray ) )$allSitesNeededFilteredByCustomersArray[ $siteCustomerName ] = [] ;
            $allSitesNeededFilteredByCustomersArray[ $siteCustomerName ][] = $userSite->getSiteId();

        }

        //On recupere le manager de la connection admin et le repository de l entité Customer
        $adminManager = $this->_registry->getManager('default');
        $customerRepo = $adminManager->getRepository(Customer::class) ;

        //On va chercher tous les objets customers (enseigne) et on les store  dans un nouveau tableau qui contiendra les enseignes existantes en bases avec le nom de chaque enseigne  en guise de clé
        $allCustomers = $customerRepo->findBy(['name' => array_keys($allSitesNeededFilteredByCustomersArray)]);
        $allCustomersIndexedByName = [];

        foreach($allCustomers as $customer){
            $allCustomersIndexedByName[ $customer->getName() ] = $customer ;
        }

        //On refait une boucle en verifiant cette fois que les enseignes recuperés de l objet user en parametre existent bien en base . SI tel est le gars on se connecte à chaque base enseigne  et on va recuperer tous les objets sites à partir des ids de ceux ci renseignes  dans l object user fournit en argument
        foreach( $allSitesNeededFilteredByCustomersArray as $enseigne => $site ){

            if (!array_key_exists($enseigne,$allCustomersIndexedByName) || ! $allCustomersIndexedByName[$enseigne] instanceof Customer){
                continue ;
            }

            //on recupere l object enseigne de l iteration en cour correspondant a l enseigne renseignée dans l objet user fournit en argument
            $enseigneEntity = $allCustomersIndexedByName[ $enseigne ];
            //on se connecte a la base de cette enseigne et on recupere le repository des sites dans celle ci puis on recupere tous les sites de l enseigne appartenant à l'user d'un coup
            $adminManager = $this->_registry->getManager($enseigne);
            $siteRepo  = $adminManager->getRepository(Site::class );
            $userSites = $siteRepo->findBy(['id'=> $site]);

            //pour chacun des sites on les ajoute à l'objet customer (afin d avoir les sites triés par customer) et on les ajoute à l'objet user fournit en argument de methode
            foreach($userSites as $userSite){
                $enseigneEntity->addSite($userSite);
                $user->addSite($userSite,$enseigneEntity);
            }
        }

    }

    //Methode pour recuperer un user present dans la base admin
    public function getUserWithRoles( User $user ){

        //on recupere tous les ids des roles  present dans l objet user fournit en argument et on boucle dessus
        foreach($user->getUserRoles() as $userRole){

            //on recupere le nom de l enseigne auquel le role est atitré et l'id du  role en question
            $roleCustomer = $userRole->getCustomer();
            $siteCustomerName = $roleCustomer->getName();
            $userRoleId = $userRole->getRoleId();

            //on va chercher le manager de la base stockant l objet role de l id recuperé ,  on va chercher le repository de l entité role en question et on va chercher le l object role lié à l id  dans sa base
            $currentEm = $this->_registry->getManager($siteCustomerName);
            $roleRepo  = $currentEm->getRepository( Role::class );
            $currentRole = $roleRepo->findOneById( $userRoleId );

            //On ajoute le role recuperé dans la base à l objet user fournit en parametre
            $user->addRole($currentRole,$roleCustomer);
        }

    }

    public function getUserWithSitesById(int $id): ?User {
        $user = $this->findOneById($id);
        $this->getUserWithSites($user) ;

        return $user ;
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

    public function getUsersByCustomer(Customer $customer)
    {
        return $this->createQueryBuilder('u')
             ->select('u')
             ->where(':customer MEMBER OF u.customers')
             ->setParameter('customer', $customer)
            ->getQuery()
            ->getResult();
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
