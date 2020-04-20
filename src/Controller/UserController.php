<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\User;
use App\Entity\Admin\UserRoles;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use App\Service\ArrayHandler;
use App\Service\SessionManager;
use App\Service\UserHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PhpParser\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Customer\Role ;

//Controlleur gerant la partie utilisateur
class UserController extends AbstractController
{

    private $passwordEncoder;
    private ObjectManager $__manager;
    private SessionManager $sessionManager ;

    //attribution du service Session Manager et de l encoder des password
 public function __construct(UserPasswordEncoderInterface $passwordEncoder)
{
    $this->sessionManager =  new SessionManager( new Session() );
    $this->passwordEncoder = $passwordEncoder;
}



    /**
     * @Route(path="/users/create", name="user::create",methods="GET|POST")
     * @return Response
     */
    //methode  gerant la partie creation utilisateur
    public function userCreate(UserHandler $userHandlerService): Response
    {

        //creation des variables qui vont contenir les sites roles et perimeter pouvant être transmis par le createur à l user qu'il souhaite creer
        $givablesRoles =[] ;
        $givableSites = [] ;
        $givablePerimeters = [] ;

        //recuperation du manager admin
        $em = $this->getDoctrine()->getManager();

        //Recuperation du Repository User
        $userRepo = $em->getRepository(User::class) ;
        $perimeterRepo = $em->getRepository(Perimeter::class);


        /** _____________________________ PARTIE USER ________________________________________ */
        $creatorUser = $userHandlerService->getUserFromSession() ;
        if( ! $creatorUser instanceof User ) throw new \Error('Impossible to find CreatorInformations') ;

        $creatorUserFromDb = $userRepo->getUserWithSitesById( $creatorUser->getId() ) ;
        if( ! $creatorUserFromDb instanceof User ) throw new \Error('Impossible to find creator in Db') ;


        /** _____________________________ PARTIE PERIMETRE ________________________________________ */

        //On recupere le perimetre du createur depuis l utilisateur recuoere depuis la session
        $userCreatorPerimeter = $creatorUserFromDb->getPerimeter();


        //On va chercher tous les perimetre egals ou superieur qui seront ceux que le createur peut donner
        $givablePerimeters = $perimeterRepo ->findPerimeterByLevelEqualOrBellow( $userCreatorPerimeter->getLevel() );



        /** _____________________________ PARTIE ROLE ________________________________________ **/

        //On recupere les roles de l utilisateur depuis la session
        $creatorUserRoles = $creatorUser->getRoles();

        //Boucle sur les roles afin de recuperer tous les roles que le createur pourra donner a l utilisateur qu il cree. La fonction a été créé de manniere a ne pas crasher si un des roles recuperés depuis la session est non conforme . Il ne sera simplement pas traité
        foreach( $creatorUserRoles as $creatorUserRoleByCustomer ) {

            //On recupere d'abord l objet customer stockant les relations roles avec les bases appartenant au customer. Ceux ci sont filtré selon le customer auquel ils appartiennent.
            if( $creatorUserRoleByCustomer instanceof Customer ) {
                //On recupere le role pour l enseigne traité et on controle son type .
                $customerRole = $creatorUserRoleByCustomer->getRole() ;
                if( $customerRole instanceof Role ) {
                    $allManagers = $this->getDoctrine()->getManagers() ;
                    if( isset ( $allManagers[ $creatorUserRoleByCustomer->getName() ] ) ){
                        $currentEm =  $allManagers[ $creatorUserRoleByCustomer->getName() ] ;
                        $roleRepo = $currentEm->getRepository(Role::class);

                        //On recupere tous les roles ayant un level egal ou superieur a celui du createur et on verifie bien qu on a recu un tableau et pas une valeur null.
                        $givableRolesFrombase  = $roleRepo->getRolesByLevelBellow($customerRole->getLevel());

                        //Si la valeur n est pas null on l ajoute aux roles ajoutables par le createur.
                        if( is_array( $givableRolesFrombase ) ) $givablesRoles[ $creatorUserRoleByCustomer->getId() ] = [
                            'customer' => $creatorUserRoleByCustomer ,
                            'roles' => $givableRolesFrombase
                        ];
                    }
                }
            }
        }

        /** _____________________________ PARTIE SITE ________________________________________ **/

        //On les place dans la variable contenant tous les sites qu il pourra donner a l utilisateur qu il cree
        $givableSites = $creatorUser->getSites() ; ;

        return $this->render('user/create.html.twig', [
            'givablePerimeters' => $givablePerimeters ,
            'givableSites'   => $givableSites ,
            'givableRoles'   => $givablesRoles
        ]) ;

    }

    /**
     * @Route(path="/users/edit/{id}", name="user::edit",methods="GET|POST")
     * @return Response
     */
    //methode  gerant la partie modification utilisateur
    public function userEdit(int $id, UserHandler $userHandlerService): Response
    {



        $givablesRoles = [] ;
        $userToModifyRoles = [] ;
        $userToModifySites = [] ;

        //Recuperation de l entity manager pour la connection admin
        $em = $this->getDoctrine()->getManager() ;

        //Recuperation du Repository User
        $userRepo = $em->getRepository(User::class) ;
        $perimeterRepo = $em->getRepository(Perimeter::class);

        /** _____________________________ PARTIE USER ________________________________________ */
      $creatorUser = $userHandlerService->getUserFromSession() ;
            if( ! $creatorUser instanceof User ) throw new \Error('Impossible to find CreatorInformations') ;


        $creatorUserFromDbWithSites = $userRepo->getUserWithSitesById( $creatorUser->getId() ) ;
        if( ! $creatorUserFromDbWithSites instanceof User ) throw new \Error('Impossible to find creator in Db') ;


        $userToModifyFromDb = $userRepo->findOneById($id) ;
        if( ! $userToModifyFromDb instanceof User ) throw new \Error('Impossible to find User to Modify') ;

                        //Recuperation de l user en base


        /** _____________________________ PARTIE PERIMETRE ________________________________________ */

        //On recupere le perimetre du createur depuis l utilisateur recuoere depuis la session
        $userCreatorPerimeter = $creatorUserFromDbWithSites->getPerimeter();

        //On va chercher tous les perimetre egals ou superieur qui seront ceux que le createur peut donner
        $givablePerimeters = $perimeterRepo ->findPerimeterByLevelEqualOrBellow( $userCreatorPerimeter->getLevel() );



        /** _____________________________ PARTIE ROLE ________________________________________ **/

        //On recupere les roles du createur  depuis la session
        $creatorUserRoles = $creatorUser->getRoles();

        //Boucle sur les roles afin de recuperer tous les roles que le createur pourra donner a l utilisateur qu il cree. La fonction a été créé de manniere a ne pas crasher si un des roles recuperés depuis la session est non conforme . Il ne sera simplement pas traité
        foreach( $creatorUserRoles as $creatorUserRoleByCustomer ) {

            //On recupere d'abord l objet customer stockant les relations roles avec les bases appartenant au customer. Ceux ci sont filtré selon le customer auquel ils appartiennent.
            if( $creatorUserRoleByCustomer instanceof Customer ) {
                //On recupere le role pour l enseigne traité et on controle son type .
                $customerRole = $creatorUserRoleByCustomer->getRole() ;
                if( $customerRole instanceof Role ) {
                    $allManagers = $this->getDoctrine()->getManagers() ;
                    if( isset ( $allManagers[ $creatorUserRoleByCustomer->getName() ] ) ){
                        $currentEm =  $allManagers[ $creatorUserRoleByCustomer->getName() ] ;
                        $roleRepo = $currentEm->getRepository(Role::class);

                        //On recupere tous les roles ayant un level egal ou superieur a celui du createur et on verifie bien qu on a recu un tableau et pas une valeur null.
                        $givableRolesFrombase  = $roleRepo->getRolesByLevelBellow($customerRole->getLevel());

                        //Si la valeur n est pas null on l ajoute aux roles ajoutables par le createur.
                        if( is_array( $givableRolesFrombase ) ) $givablesRoles[ $creatorUserRoleByCustomer->getId() ] = [
                            'customer' => $creatorUserRoleByCustomer ,
                            'roles' => $givableRolesFrombase
                        ];
                    }
                }
            }
        }


        foreach($userToModifyFromDb->getUserRoles() as $userRoleEntry){
            $userToModifyRoles[ $userRoleEntry->getCustomer()->getId() ] = [
                'customer' => $userRoleEntry->getCustomer() ,
                'role' =>  $userRoleEntry->getRoleId()
            ];
        }


        /** _____________________________ PARTIE SITE ________________________________________ **/
        $userToModifySitesFromDb = $userToModifyFromDb->getSitesIds() ;

        //Creation du tableau des sites indexés par le nom de leur enseigne
        foreach( $userToModifySitesFromDb as $userToModifySite ) {
            //Si l entrée de l enseigne du site en cour n existe pas on la cree et lui attribue un tableau vide qui contiendra ses sites
            if(!isset( $userToModifySites[ $userToModifySite->getCustomer()->getId() ] ) && is_int( $userToModifySite->getSiteId() ) ) $userToModifySites[ $userToModifySite->getCustomer()->getId() ] = [] ;

            $userToModifySites[ $userToModifySite->getCustomer()->getId() ][ $userToModifySite->getSiteId() ] =  $userToModifySite ;
        }

        $givableSites = $creatorUserFromDbWithSites->getSites();


            return $this->render("user/modify.html.twig", [
                'userToModify' => $userToModifyFromDb ,
                'givablePerimeters' => $givablePerimeters ,
                'givableRoles'   => $givablesRoles ,
                'userToModifyRoles'=> $userToModifyRoles,
                'userToModifySites' => $userToModifySites ,
                'givableSites'      => $givableSites
            ]);

    }

    /**
     * @Route(path="/users", name="users:view")
     *
     * @param Request $request
     * @return Response
     */
    public function userView(Request $request): Response
    {
        //Recuperation de l entité manager pour la conenction admin
       $em =  $this->getDoctrine()->getManager();
       //Recuperation du repository de l entité Customer correspondant à l'enseigne
       $customerRepo = $em->getRepository(Customer::class);

       $currentCustomer = $customerRepo->findOneByName('kfc');
       $usersInCustomer = $currentCustomer->getUsers() ;

        return $this->render("settings/setting-user.html.twig", [
            'usersInCustomer' => $usersInCustomer
        ]);

    }


    //Methode gerant le retour du formulaire de creation utilsiateur avec les donnees entrées par le createur
    /**
     * @Route(path="/users/process/edit", name="users::edit::process",methods="POST")
     * @return Response
     */
    public function userModifyProcess(Request $request, ArrayHandler $arrayHandler): Response
    {
        $userToModifyFormDatas = $request->get('user');

        if(! isset( $userToModifyFormDatas[ 'user' ] ) ) throw new \Error('Invalid argument for user ');
        if(! isset( $userToModifyFormDatas[ 'roles' ] ) || !isset( $userToModifyFormDatas[ 'roles' ][ 'enseigne' ] ) || !is_array( $userToModifyFormDatas[ 'roles' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Role Entry ');
        if(! isset( $userToModifyFormDatas[ 'sites' ] ) || !isset( $userToModifyFormDatas[ 'sites' ][ 'enseigne' ] ) || !is_array( $userToModifyFormDatas[ 'sites' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Site Entry ');
        if(! isset( $userToModifyFormDatas[ 'perimeter' ] ) ) throw new \Error('Invalid argument for Perimeter ');
        if(! isset( $userToModifyFormDatas[ 'email' ] ) ) throw new \Error('Invalid argument for email ');
        if(! isset( $userToModifyFormDatas[ 'phone' ] ) ) throw new \Error('Invalid argument for phone ');
        if(! isset( $userToModifyFormDatas[ 'fristname' ] ) ) throw new \Error('Invalid argument for firstname ');
        if(! isset( $userToModifyFormDatas[ 'lastname' ] ) ) throw new \Error('Invalid argument for lastname ');


        //Recuperation de l entite manager de la base admin
        $em = $this->getDoctrine()->getManager('default');

        //On recupere le createur  depuis la session
        $userCreatorFromSession = $this->sessionManager->get('user');

        //Recuperation du repo User
        $userRepo = $em->getRepository(User::class) ;
       $userRoleRepo = $em->getRepository(UserRoles::class) ;
       $userSiteRepo = $em->getRepository(UserSites::class) ;
       $perimeterRepo = $em->getRepository(Perimeter::class) ;


        if(! $userCreatorFromSession instanceof  User ) throw new Error('Impossible to find user creator informations') ;
        $userCreatorFromDb = $userRepo->findOneById($userCreatorFromSession->getId());

        //Si on trouve pas l user en session dans la base on envoit une erreur
        if( ! $userCreatorFromDb instanceof User) throw new \Error('Impossible to get User Informations') ;

        //Recuperation de l user a modifier partir de la Db
        $userToModifyFromDb = $userRepo->findOneById($userToModifyFormDatas[ 'user' ]);
        if (! $userToModifyFromDb instanceof User ) throw new \Error('Impossible to find User') ;

        $userToModifyFromDb->setEmail( $userToModifyFormDatas[ 'email' ] ) ;
        $userToModifyFromDb->setPhoneNumber( $userToModifyFormDatas[ 'phone' ] ) ;
        $userToModifyFromDb->setFirstName($userToModifyFormDatas['fristname']) ;
        $userToModifyFromDb->setLastName($userToModifyFormDatas['lastname']) ;

        /** _______________________PERIMETER______________________________  */

        $perimeterSelectedInForm = $userToModifyFormDatas[ 'perimeter' ] ;
        $selectedPerimeterFromBase = $perimeterRepo->findOneById( $perimeterSelectedInForm ) ;

        if( ! $selectedPerimeterFromBase instanceof Perimeter ) throw new Error('Impossible to find Selected Perimeter In base') ;
        if($userCreatorFromDb->getPerimeter()->getLevel() > $selectedPerimeterFromBase->getLevel()) throw new Error('creator not Allowed to attribute this periemter');
        $userToModifyFromDb->setPerimeter($selectedPerimeterFromBase) ;


        /**_____________________________________________ROLE______________________________*/

        $allRolesToImports = [] ;
        $allRoleImported = [] ;
        $handledUserRolesEntryFiltredByCustomerId = [] ;
        $useCreatorRolesEntryFiltredByCustomerId = [] ;

        foreach( $userCreatorFromDb->getUserRoles() as $roleEntry) {
            if( ! isset( $allRolesToImports[ $roleEntry->getCustomer()->getId() ] ) &&  is_int($roleEntry->getRoleId() ) ) $allRolesToImports[ $roleEntry->getCustomer()->getId() ] = [
                'enseigne' => $roleEntry->getCustomer() ,
                'roles' => []
            ];
            $allRolesToImports[ $roleEntry->getCustomer()->getId() ]['roles'][] = $roleEntry->getRoleId() ;
            $useCreatorRolesEntryFiltredByCustomerId[ $roleEntry->getCustomer()->getId() ] =  $roleEntry ;
        }

        foreach($userToModifyFormDatas[ 'roles' ]['enseigne'] as $userToModifyRoleEnseigneId => $userToModifyRoleEntry){

            if( ! isset( $allRolesToImports[ $userToModifyRoleEnseigneId ] ) ) throw new \Error('some roles selected cannot be affected') ;
            if( ! in_array($userToModifyRoleEntry , $allRolesToImports[ $userToModifyRoleEnseigneId  ]['roles'] ) )   $allRolesToImports[ $userToModifyRoleEnseigneId ]['roles'][] = $userToModifyRoleEntry ;
        }

        foreach($allRolesToImports as $allRolesToImportsByCustomer) {

            $currentCustomer = $allRolesToImportsByCustomer['enseigne'] ;
            $currentCustomerRoles = $allRolesToImportsByCustomer['roles'] ;

           $customerRoles =  $userRoleRepo->getRolesInCustomer($currentCustomer ,  $currentCustomerRoles ) ;

            $allRoleImported[ $customerRoles['customer']->getId() ] = [
                'customer'   => $customerRoles['customer'],
                'roles'      => $arrayHandler->filterArrayById( $customerRoles['roles'] )
            ];
        }


        foreach($userToModifyFromDb->getUserRoles() as $userRoleEntry ) {
            $handledUserRolesEntryFiltredByCustomerId[ $userRoleEntry->getCustomer()->getId() ] =  $userRoleEntry ;
        }

        foreach($userToModifyFormDatas[ 'roles' ]['enseigne'] as $userToModifyRoleEnseigneId => $userToModifyRoleEntry) {
            //On verifit d'abord qu'il a bien le droit d'avoir ce role

            $roleToAddToModifyUser = $allRoleImported[ $userToModifyRoleEnseigneId ] ['roles'] [$userToModifyRoleEntry] ;

            if(!isset($useCreatorRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId])) throw new Error('User creator cannot give ROle for this customer') ;
            $userCreatorEntryForThisCustomer = $useCreatorRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId] ;
            $userCreatorRoleForTHisCustomer = $allRoleImported[$userToModifyRoleEnseigneId]['roles'][$userCreatorEntryForThisCustomer->getRoleId()] ;  ;

            if( ! $userCreatorRoleForTHisCustomer instanceof Role ) throw new Error('Impossible to find Creator Role in imported Roles') ;

            if($userCreatorRoleForTHisCustomer->getLevel() > $roleToAddToModifyUser->getLevel() ) throw new Error('Creator not allowed to choose this role') ;


            if(! isset($handledUserRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId] ) ){
                $handledUserRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId] = $newUserRoleToAddToModifiedUser = new UserRoles();
                $newUserRoleToAddToModifiedUser->setCustomer($useCreatorRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId]->getCustomer()) ;
                $newUserRoleToAddToModifiedUser->setRoleId( $roleToAddToModifyUser->getId() ) ;
                $newUserRoleToAddToModifiedUser->setUser($userToModifyFromDb);
            }


            $handledUserRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId]->setRoleId($roleToAddToModifyUser->getId());

        }


       $roleRemovedEnseigneIds = array_diff(array_keys($handledUserRolesEntryFiltredByCustomerId), array_keys($userToModifyFormDatas[ 'roles' ]['enseigne'] ));


        foreach($roleRemovedEnseigneIds as $roleRemovedEnseigneId){
            if( isset( $handledUserRolesEntryFiltredByCustomerId[$roleRemovedEnseigneId] ) ) {
                $userToModifyFromDb->removeUserRole($handledUserRolesEntryFiltredByCustomerId[$roleRemovedEnseigneId]);
            }
        }

        /**_____________________________________SITES_____________________________________**/

        //Creation dun tableau contenant les sites possedé par le createur classé par enseignes afin de pouvoir effectuer une seule connection dans chaque  base enseigne pour la gestion des sites
        $userSitesWithCustomerIdAsKey= [] ;
        $userToModifySitesEntryFiltredByCustomerId =[] ;

        foreach($userCreatorFromDb->getSitesIds() as $siteEntry){
            $userSitesWithCustomerIdAsKey [ $siteEntry->getCustomer()->getId() ][$siteEntry->getSiteId()] = $siteEntry;
        }
        foreach($userToModifyFromDb->getSitesIds() as $siteEntry){
            $userToModifySitesEntryFiltredByCustomerId [ $siteEntry->getCustomer()->getId() ][$siteEntry->getSiteId()] = $siteEntry;
        }


        //On boucle sur les  sites que le  createur souhaite donner au nouvel users ( ceux ci sont classé par enseigne)
        foreach($userToModifyFormDatas[ 'sites' ][ 'enseigne' ] as $siteEnseigneId => $siteIdInEnseigne) {
            //On verifit bien que l enseigne traité apparait bien dans le tableau des sites possedés par  le createur et que la valeur jointe est bien un tableau.
            if( isset( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) && is_array( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) ){

                //On recupere le tableau contenant les sites possedés par le createur
                $userCreatorSiteEntries = $userSitesWithCustomerIdAsKey[ $siteEnseigneId ] ;
                $userToModifySiteEntries = $userToModifySitesEntryFiltredByCustomerId[ $siteEnseigneId ] ;

                //On boucle sur le tableau des ids des sites souhaitant etre transmis a l user créé (renseignés dans le formulaire)
                foreach( $siteIdInEnseigne as $userToModifySiteId) {

                    // On verifit bien que le createur possede bien le site qu 'il souhaite transmettre en verifiant que l id du site qu il veut donner est bien presant dans le tableau de ses sites
                   if( ! isset ( $userCreatorSiteEntries [ $userToModifySiteId ] ) ) throw new Error('one or more site selected cannot be attributed by the creator') ;
                   if( isset( $userToModifySiteEntries[ $userToModifySiteId] ) ) continue ;

                        //Si oui on cree l entité  qui correspondra a l entree dans la table de relation de la base admin qui precisera l id et le nom du customer correspondant a l entree du site souhaitant etre donné dans la base correspondante.
                        $creatorSiteEntry = $userCreatorSiteEntries [ $userToModifySiteId ] ;
                        $newUserSiteEntry  = new UserSites();
                        $newUserSiteEntry->setCustomer( $creatorSiteEntry->getCustomer() ) ;
                        $newUserSiteEntry->setSiteId( $creatorSiteEntry->getSiteId() );
                        $userToModifyFromDb->addSitesId($newUserSiteEntry);

                }

            }
        }

        foreach($userToModifySitesEntryFiltredByCustomerId as $customer => $customerSites){

            if(!isset( $userToModifyFormDatas[ 'sites' ][ 'enseigne' ][ $customer ] ) ){
                foreach($customerSites as $userSiteEntry){
                    $userToModifyFromDb->removeSitesId($userSiteEntry);
                    $em->remove($userSiteEntry);
                    continue;
                }
            }
            foreach($customerSites as  $siteId => $userSiteEntry) {
                if( !in_array($siteId, $userToModifyFormDatas[ 'sites' ][ 'enseigne' ][ $customer ]) ) {
                    $userToModifyFromDb->removeSitesId($userSiteEntry);
                    $em->remove($userSiteEntry);
                }
            }
        }

      //  dd($userToModifyFromDb);
        $em->persist($userToModifyFromDb);
        $em->flush();


        return $this->redirectToRoute('users:view');

    }
    //Methode gerant le retour du formulaire de creation utilsiateur avec les donnees entrées par le createur
    /**
     * @Route(path="/users/create/process", name="users::create::process",methods="POST")
     * @return Response
     */
    public function userCreateProcess(Request $request,ArrayHandler $arrayHandler): Response
    {

//On recupere l object user depuis la requete post
        $userToCreateFormDatas = $request->get('user');


        //On verifie que tous les champs importants ont bien ete renseigne.
        //TODO controler le type
        if(! isset( $userToCreateFormDatas[ 'roles' ] ) || !isset( $userToCreateFormDatas[ 'roles' ][ 'enseigne' ] ) || !is_array( $userToCreateFormDatas[ 'roles' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Role Entry ');
        if(! isset( $userToCreateFormDatas[ 'sites' ] ) || !isset( $userToCreateFormDatas[ 'sites' ][ 'enseigne' ] ) || !is_array( $userToCreateFormDatas[ 'sites' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Site Entry ');
        if(! isset( $userToCreateFormDatas[ 'perimeter' ] ) ) throw new \Error('Invalid argument for Perimeter ');
        if(! isset( $userToCreateFormDatas[ 'email' ] ) ) throw new \Error('Invalid argument for email ');
        if(! isset( $userToCreateFormDatas[ 'phone' ] ) ) throw new \Error('Invalid argument for phone ');
        if(! isset( $userToCreateFormDatas[ 'fristname' ] ) ) throw new \Error('Invalid argument for firstname ');
        if(! isset( $userToCreateFormDatas[ 'lastname' ] ) ) throw new \Error('Invalid argument for lastname ');

        //Recuperation de l entite manager de la base admin
        $em = $this->getDoctrine()->getManager('default');

        //On recupere le createur  depuis la session
        $userCreatorFromSession = $this->sessionManager->get('user');


        //Recuperation du repo User
        $userRepo = $em->getRepository(User::class) ;
        $userRoleRepo = $em->getRepository(UserRoles::class) ;
        $userSiteRepo = $em->getRepository(UserSites::class) ;
        $perimeterRepo = $em->getRepository(Perimeter::class) ;

        if(! $userCreatorFromSession instanceof  User ) throw new Error('Impossible to find user creator informations') ;

        $userCreatorFromDb = $userRepo->findOneById($userCreatorFromSession->getId()) ;


        //Si on trouve pas l user en session dans la base on envoit une erreur
        if( ! $userCreatorFromDb instanceof User) throw new \Error('Impossible to get User Informations') ;

        //Creation de l object stockant le nouvel user
        $newUserEntity = new User() ;

        //Attribution des infos principales nom prenom email telephone et attributino d un pass par defaut encodé
        $newUserEntity->setFirstName( $userToCreateFormDatas[ 'fristname' ] );
        $newUserEntity->setLastName($userToCreateFormDatas['lastname']) ;
        $newUserEntity->setEmail($userToCreateFormDatas[ 'email' ] ) ;
        $newUserEntity->setPhoneNumber($userToCreateFormDatas[ 'phone' ]);

        $newUserEntity->setPassword($this->passwordEncoder->encodePassword(
            $newUserEntity,
            'test'
        ));

        /** _______________________PERIMETER______________________________  */

        //Recuperation de l id  du perimetre que le createur a souhaité donner au nouvel user
        $perimeterSelectedInForm = $userToCreateFormDatas[ 'perimeter' ] ;

        $selectedPerimeterFromBase = $perimeterRepo->findOneById($perimeterSelectedInForm) ;

        //Si on trouve pas de Perimetre lie a cet id en base on envoit une erreur
        if( ! $selectedPerimeterFromBase instanceof Perimeter ) throw new \Error('Impossible to recognize gived Perimeter') ;

        //Si le niveau perimetre du createur est superieur au niveau de celui qu il souhaite donner au nouvel utilisateur on envoit une erreur car il n a pas le droit de donner ce perimetre
        if($userCreatorFromDb->getPerimeter()->getLevel() > $selectedPerimeterFromBase->getLevel()) throw new Error('creator not Allowed to attribute this periemter');
        $newUserEntity->setPerimeter($selectedPerimeterFromBase) ;

        /**_____________________________________________ROLE______________________________*/

        $allRolesToImports = [] ;
        $allRoleImported = [] ;
        $handledUserRolesEntryFiltredByCustomerId = [] ;
        $useCreatorRolesEntryFiltredByCustomerId = [] ;

        foreach( $userCreatorFromDb->getUserRoles() as $roleEntry) {
            if( ! isset( $allRolesToImports[ $roleEntry->getCustomer()->getId() ] ) &&  is_int($roleEntry->getRoleId() ) ) $allRolesToImports[ $roleEntry->getCustomer()->getId() ] = [
                'enseigne' => $roleEntry->getCustomer() ,
                'roles' => []
            ];
            $allRolesToImports[ $roleEntry->getCustomer()->getId() ]['roles'][] = $roleEntry->getRoleId() ;
            $useCreatorRolesEntryFiltredByCustomerId[ $roleEntry->getCustomer()->getId() ] =  $roleEntry ;
        }

        foreach($userToCreateFormDatas[ 'roles' ]['enseigne'] as $userToModifyRoleEnseigneId => $userToModifyRoleEntry){

            if( ! isset( $allRolesToImports[ $userToModifyRoleEnseigneId ] ) ) throw new \Error('some roles selected cannot be affected') ;
            if( ! in_array($userToModifyRoleEntry , $allRolesToImports[ $userToModifyRoleEnseigneId  ]['roles'] ) )   $allRolesToImports[ $userToModifyRoleEnseigneId ]['roles'][] = $userToModifyRoleEntry ;
        }

        foreach($allRolesToImports as $allRolesToImportsByCustomer) {

            $currentCustomer = $allRolesToImportsByCustomer['enseigne'] ;
            $currentCustomerRoles = $allRolesToImportsByCustomer['roles'] ;

            $customerRoles =  $userRoleRepo->getRolesInCustomer($currentCustomer ,  $currentCustomerRoles ) ;

            $allRoleImported[ $customerRoles['customer']->getId() ] = [
                'customer'   => $customerRoles['customer'],
                'roles'      => $arrayHandler->filterArrayById( $customerRoles['roles'] )
            ];
        }

        foreach($userToCreateFormDatas[ 'roles' ]['enseigne'] as $userToCreateRoleEnseigneId => $userToCreateRoleEntry) {
            //On verifit d'abord qu'il a bien le droit d'avoir ce role

            $roleToAddToModifyUser = $allRoleImported[ $userToCreateRoleEnseigneId ] ['roles'] [$userToCreateRoleEntry] ;

            if(!isset($useCreatorRolesEntryFiltredByCustomerId[$userToCreateRoleEnseigneId])) throw new Error('User creator cannot give ROle for this customer') ;
            $userCreatorEntryForThisCustomer = $useCreatorRolesEntryFiltredByCustomerId[$userToModifyRoleEnseigneId] ;
            $userCreatorRoleForTHisCustomer = $allRoleImported[$userToCreateRoleEnseigneId]['roles'][$userCreatorEntryForThisCustomer->getRoleId()] ;  ;

            if( ! $userCreatorRoleForTHisCustomer instanceof Role ) throw new Error('Impossible to find Creator Role in imported Roles') ;

            if($userCreatorRoleForTHisCustomer->getLevel() > $roleToAddToModifyUser->getLevel() ) throw new Error('Creator not allowed to choose this role') ;

            $handledUserRolesEntryFiltredByCustomerId[$userToCreateRoleEnseigneId] = $newUserRoleToAddToModifiedUser = new UserRoles();
            $newUserRoleToAddToModifiedUser->setCustomer($useCreatorRolesEntryFiltredByCustomerId[$userToCreateRoleEnseigneId]->getCustomer()) ;
            $newUserRoleToAddToModifiedUser->setRoleId( $roleToAddToModifyUser->getId() ) ;
            $newUserRoleToAddToModifiedUser->setUser($newUserEntity);
            $newUserEntity->addUserRole($newUserRoleToAddToModifiedUser);

        }



        /**_____________________________________SITES_____________________________________**/

        //Creation dun tableau contenant les sites possedé par le createur classé par enseignes afin de pouvoir effectuer une seule connection dans chaque  base enseigne pour la gestion des sites
        $userSitesWithCustomerIdAsKey= [] ;
        foreach($userCreatorFromDb->getSitesIds() as $siteEntry){
            $userSitesWithCustomerIdAsKey [ $siteEntry->getCustomer()->getId() ][$siteEntry->getSiteId()] = $siteEntry;
        }

        //On boucle sur les  sites que le  createur souhaite donner au nouvel users ( ceux ci sont classé par enseigne)
        foreach($userToCreateFormDatas[ 'sites' ][ 'enseigne' ] as $siteEnseigneId => $siteIdInEnseigne) {
            //On verifit bien que l enseigne traité apparait bien dans le tableau des sites possedés par  le createur et que la valeur jointe est bien un tableau.
            if( isset( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) && is_array( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) ){

                //On recupere le tableau contenant les sites possedés par le createur
                $userSiteEntries = $userSitesWithCustomerIdAsKey[ $siteEnseigneId ] ;

               //On boucle sur le tableau des ids des sites souhaitant etre transmis a l user créé (renseignés dans le formulaire)
                foreach( $siteIdInEnseigne as $newUserSiteId) {
                    // On verifit bien que le createur possede bien le site qu 'il souhaite transmettre en verifiant que l id du site qu il veut donner est bien presant dans le tableau de ses sites
                    if(! isset($userSiteEntries [ $newUserSiteId ])) throw new Error('You re not allowed to attributes some site') ;

                        //Si oui on cree l entité  qui correspondra a l entree dans la table de relation de la base admin qui precisera l id et le nom du customer correspondant a l entree du site souhaitant etre donné dans la base correspondante.
                        $creatorSiteEntry = $userSiteEntries [ $newUserSiteId ] ;
                        $newUserSiteEntry  = new UserSites();
                        $newUserSiteEntry->setCustomer( $creatorSiteEntry->getCustomer() ) ;
                        $newUserSiteEntry->setSiteId( $creatorSiteEntry->getSiteId() );
                        $em->persist($newUserSiteEntry);

                        $newUserEntity->addSitesId($newUserSiteEntry);


                }

            }
        }

        //On persiste l object contenant le nouvel user et on flush . l user est créé!!
        $em->persist($newUserEntity);
       // dd($em);
        $em->flush();

        return $this->redirectToRoute('users:view') ;
    }

    /**
     * @Route(path="/users/delete/{id}", name="users::delete",methods="GET")
     * @return Response
     */
    public function userDelete(int $id) {
        $em = $this->getDoctrine()->getManager() ;
        $userRepo = $em->getRepository(User::class );

        $userToDelete  = $userRepo->findOneById( $id ) ;

        if( ! $userToDelete instanceof User ) throw new Error('cannot find User to delete') ;

        $em->remove($userToDelete);
        $em->flush();
        return $this->redirectToRoute('users:view');
    }


    /**
     * @Route("path=/logout", name="user::logout")
     * This method can be blank - it will be intercepted by the logout key on your firewall (see : config/packages/security.yaml)
     */
    public function logout() { }


    /**
     *
     * @Route(path="/password/forget", name="user::password_forget", methods="GET")
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
	public function passwordForget(Request $request): Response
    {
        return $this->render("security/resetPassword.html.twig");
    }


//    /**
//     * @Route(path="/send/password/reset/email", name="user::send_password_reset_email", methods="POST")
//     *
//     * @param Request $request
//     * @param EmailSenderService $mailer
//     * @return Response
//     * @throws Exception
//     */
//	public function sendPasswordResetEmail(Request $request, EmailSenderService $mailer, TokenGeneratorService $tokenGeneratorService): Response
//    {
//
//        if(is_null($request->request->get('username')))
//        {
//            throw new Exception("Missing 'username' parameter !");
//        }
//
//        $user = $this->getDoctrine()->getRepository(User::class)->findOneByUsername($request->request->get('username'));
//
//        if (!$user) {
//            throw new Exception("User not found !");
//        }
//
//        $token = $tokenGeneratorService->generate(64);
//
//        $userEmail = $user->getEmail();
//
//        $user->setUserPassword("")
//             ->setPasswordResetToken($token);
//
//        // maj de la base de donnée
//        $this->getDoctrine()->getManager()->flush();
//
//
//        $mailer->sendEmail(
//                        "cbaby@infoway.fr", $userEmail,
//                        "cbaby@infoway.fr", "Reset my password", $this->renderView(
//                        "security/resetPasswordEmail.html.twig", [
//                        'token' => $token,
//                        'username' => strtoupper($user->getUsername())
//                        ]
//                    ), 'text/html'
//                );
//
//        // message flash dans la session
//        (new Session())->getFlashBag()->add("message", "Merci de vérifier votre boîte mail, un mail contenant le lien pour réinitialiser votre mot de passe vous a été envoyé !");
//
//        return $this->redirectToRoute("user::password_forget");
//
//    }
//
//
//    /**
//     * Renvoie une vue permettant de reinitialisser son mot de passe
//     *
//     * @Route(path="/reset/password/{password_reset_token}", name="user::reset_password", methods="GET|POST")
//     *
//     * @param Request $request
//     * @param UserPasswordEncoderInterface $passwordEncoder
//     * @param User $user
//     * @return Response
//     * @throws Exception
//     */
//	public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
//    {
//
//        if($request->isMethod("POST"))
//        {
//            if(!empty($request->request->get("new_password")))
//            {
//
//                // on rajoute le token dans le champ "password_reset_token"
//                $user->setPassword($passwordEncoder->encodePassword($user, $request->get("new_password")));
//
//                // le token a été utilisé, on peut le supprimer
//                $user->setPasswordResetToken("");
//
//                // on mets à jour la base de donnée
//                $this->getDoctrine()->getManager()->flush();
//
//                // message flash dans la session
//                (new Session())->getFlashBag()->add("message", "Votre mot de passe a bien été modifié ! Vous pouvez vous connecter <a href='". $this->generateUrl("user::login") ."'>içi</a>");
//            }
//            else
//            {
//                throw new Exception("Missing new_password parameter !");
//            }
//        }
//
//        return $this->render("security/resetPassword.html.twig");
//
//    }
//
//    /**
//     * @Route(path="/registration/confirm/{registration_token}", name="user::registration_confirmation", methods="GET|POST")
//     *
//     * @param User $user
//     * @return Response
//     */
//     public function registrationConfirm(User $user): Response
//     {
//
//         // uncomment this if you want delete registration token on account confirmation
//        /*$user->setRegistrationToken("");
//
//        $this->getDoctrine()->getManager()->flush();*/
//
//        return $this->render("security/confirmInscription.html.twig");
//
//     }
//
//
//    /**
//     * @Route(path="/users/list", name="user::showAllUsers")
//     * @return Response
//     */
//    public function showAllUsers()
//    {
//
//        $this->__manager = $this->getDoctrine()->getManager('default');
//
//        return $this->render(
//            'user/user.showAll.html.twig', [
//            'users' => $this->__manager->getRepository(User::class)->findAll()
//        ]);
//
//    }
//
//
//    /**
//     * @Route(path="/user/{id}/permissions", name="user::showUserPermissions")
//     * @return Response
//     */
//    public function showUserPermissions(User $user)
//    {
//
//        $this->__manager = $this->getDoctrine()->getManager('default');
//
//        $permissionsHandler = new PermissionsHandler($this->__manager);
//
//        $userPermissions = $permissionsHandler->getUserPermissions($user, true);
//        $userRolePermissions = $permissionsHandler->getUserRolePermissions($user, false);
//
//        $actions = $this->__manager->getRepository(Action::class)->findAll();
//
//        //dd($userRolePermissions, $userPermissions);
//
//        dump($userRolePermissions, $userPermissions);
//
//        return $this->render('user/user.editPermissions.html.twig', [
//            'user' => (object) ['id' => $user->getId(), 'username' => $user->getUsername(), 'role' => $user->getRole()->getName()],
//            'userPermissions' => $userPermissions,
//            'rolePermissions' => $userRolePermissions,
//            'actions' => $actions
//        ]);
//
//    }
//
//
//    /**
//     * @Route(path="/edit/user/{id}/permissions", name="user::editUserPermissions", methods="POST")
//     */
//    public function editUserPermissions(User $user, Request $request)
//    {
//
//        if($request->request->get('permissions') === null)
//            throw new Exception(sprintf("Internal Error : Missing or invalid '%s' parameter !",'permissions'));
//
//        $permissions = json_decode($request->request->get('permissions'));
//
//        // before update
//        //dump(sizeof($user->getPermissions()->getValues()));
//
//        $updateState = $this->updateUserPermissions($user, $permissions);
//
//        // after update
//        //dd(sizeof($user->getPermissions()->getValues()));
//
//        return new JsonResponse([
//            'status' => 200,
//            'message' => "200 OK"
//        ]);
//
//    }
//
//
//    private function updateUserPermissions(User $user, array $permissions)
//    {
//
//        $userPermissionsDefaultSize = sizeof($user->getPermissions()->getValues());
//
//        foreach ($permissions as $permission_json)
//        {
//
//            $permission = $this->getDoctrine()->getManager('default')->getRepository(Permission::class)->findOneById($permission_json->__id);
//
//            if(!$permission_json->__state AND $user->getPermissions()->contains($permission))
//                $user->removePermission($permission);
//
//            elseif ($permission_json->__state AND !$user->getPermissions()->contains($permission))
//                $user->addPermission($permission);
//
//        }
//
//        if(($userPermissionsDefaultSize !== $user->getPermissions()->getValues()) )
//            $this->getDoctrine()->getManager('default')->flush();
//
//        return true;
//
//    }


}