<?php


namespace App\Controller;


use App\Entity\Admin\Action;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\Permission;
use App\Entity\Admin\Subject;
use App\Entity\Admin\User;
use App\Entity\Admin\UserRoles;
use App\Entity\Admin\UserSites;
use App\Form\Admin\UserRolesType;
use App\Form\UserType;
use App\Service\EmailSenderService;
use App\Service\PermissionsHandler;
use App\Service\SessionManager;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    public function userCreate(): Response
    {
        //recuperation du manager admin
        $em = $this->getDoctrine()->getManager();

        //creation des variables qui vont contenir les sites roles et perimeter pouvant être transmis par le createur à l user qu'il souhaite creer
        $givablesRoles = new \SplObjectStorage() ;
        $givableSites = [] ;
        $givablePerimeters = [] ;

        // _____________________________ PARTIE USER ________________________________________
        //recuperation de l user en session
        $user = $this->sessionManager->get('user');

        //Si l object qu on recupere n est pas de type user on envoit une erreur
        if( ! $user instanceof User ) {
            throw new \Error('Impossible to get User from Session');
        }

        // _____________________________ PARTIE PERIMETRE ________________________________________
        //On recupere le perimetre du createur depuis l utilisateur recuoere depuis la session
        $userPerimeterFromSession = $user->getPerimeter();

        //Si l object qu on recupere n est pas de type Perimetre on envoit une erreur
        if( ! $userPerimeterFromSession instanceof Perimeter ) {
            throw new \Error('Impossible to find User Perimeter in Session ');
        }

        //Recuperation du repository de l entite perimetre dans admin
        $perimeterRepo = $em->getRepository(Perimeter::class);
        //On va recuperer le perimetre en base pour être sur que celui de la session est totalement conforme si l object recupere n est pas de type Perimeter on envoit une erreur
        $userPerimeter = $perimeterRepo->findOneById($userPerimeterFromSession->getId());
        if( ! $userPerimeter instanceof Perimeter ) {
            throw new \Error('Impossible to find User Perimeter  ');
        }

        //On va chercher tous les perimetre egals ou superieur qui seront ceux que le createur peut donner
        $givablePerimeters = $perimeterRepo ->findPerimeterByLevelEqualOrBellow( $userPerimeter->getLevel() );



        // _____________________________ PARTIE ROLE ________________________________________

        //On recupere les roles de l utilisateur depuis la session
        $userRoles = $user->getRoles();

        //Boucle sur les roles afin de recuperer tous les roles que le createur pourra donner a l utilisateur qu il cree. La fonction a été créé de manniere a ne pas crasher si un des roles recuperés depuis la session est non conforme . Il ne sera simplement pas traité
        foreach( $userRoles as $userRoleByCustomer ) {

            //On recupere d'abord l objet customer stockant les relations roles avec les bases appartenant au customer. Ceux ci sont filtré selon le customer auquel ils appartiennent.
            if( $userRoleByCustomer instanceof Customer ) {
                //On recupere le role pour l enseigne traité et on controle son type .
                $customerRole = $userRoleByCustomer->getRole() ;
                if( $customerRole instanceof Role ) {
                    //On va chercher le Manager appartenant à la base enseigne en cour contenant le  role et le repository lié a celui ci .
                    $customererManager = $this->getDoctrine()->getManager( $userRoleByCustomer->getName() );
                    $roleRepo = $customererManager->getRepository(Role::class);

                    //On recupere tous les roles ayant un level egal ou superieur a celui du createur et on verifie bien qu on a recu un tableau et pas une valeur null.
                    $givableRolesFrombase  = $roleRepo->getRolesByLevelBellow($customerRole->getLevel());
                    //Si la valeur n est pas null on l ajoute aux roles ajoutables par le createur.
                    if( is_array($givableRolesFrombase) ) $givablesRoles[ $userRoleByCustomer ] = $givableRolesFrombase ;

                }
            }
        }

        //On recupere tous les sites du createur
       $userSites = $user->getSites() ;

        //On les place dans la variable contenant tous les sites qu il pourra donner a l utilisateur qu il cree
        $givableSites = $userSites ;

        return $this->render('user/create.html.twig', [
            'givablePerimeters' => $givablePerimeters ,
            'givableSites'   => $givableSites ,
            'givableRoles'   => $givablesRoles
        ]) ;

    }

    //Methode gerant le retour du formulaire de creation utilsiateur avec les donnees entrées par le createur
    /**
     * @Route(path="/users/create/process", name="users::create::process",methods="POST")
     * @return Response
     */
    public function userCreateProcess(Request $request): Response
    {

//On recupere l object user depuis la requete post
        $newUser = $request->get('user');


        //On verifie que tous les champs importants ont bien ete renseigne.
        //TODO controler le type
        if(! isset( $newUser[ 'roles' ] ) || !isset( $newUser[ 'roles' ][ 'enseigne' ] ) || !is_array( $newUser[ 'roles' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Role Entry ');
        if(! isset( $newUser[ 'sites' ] ) || !isset( $newUser[ 'sites' ][ 'enseigne' ] ) || !is_array( $newUser[ 'sites' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Site Entry ');
        if(! isset( $newUser[ 'perimeter' ] ) ) throw new \Error('Invalid argument for Perimeter ');
        if(! isset( $newUser[ 'email' ] ) ) throw new \Error('Invalid argument for email ');
        if(! isset( $newUser[ 'phone' ] ) ) throw new \Error('Invalid argument for phone ');
        if(! isset( $newUser[ 'fristname' ] ) ) throw new \Error('Invalid argument for firstname ');
        if(! isset( $newUser[ 'lastname' ] ) ) throw new \Error('Invalid argument for lastname ');

        //On recupere le createur  depuis la session
        $user = $this->sessionManager->get('user');
        //Recuperation de l entite manager de la base admin
        $em = $this->getDoctrine()->getManager('default');

        //Recuperation du repository User dans la base admin et recuperation de l user stocké en sesssion dans la base afin de s assurer que les  donnees sont bien celle de la base
        $userRepo = $em->getRepository(User::class) ;
        $userFromDb = $userRepo->findOneById($user->getId()) ;


        //Si on trouve pas l user en session dans la base on envoit une erreur
        if( ! $userFromDb instanceof User) throw new \Error('Impossible to get User Informations') ;

        //Creation de l object stockant le nouvel user
        $newUserEntity = new User() ;

        //Attribution des infos principales nom prenom email telephone et attributino d un pass par defaut encodé
        $newUserEntity->setFirstName( $newUser[ 'fristname' ] );
        $newUserEntity->setLastName($newUser['lastname']) ;
        $newUserEntity->setEmail($newUser[ 'email' ] ) ;
        $newUserEntity->setPhoneNumber($newUser[ 'phone' ]);

        $newUserEntity->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'test'
        ));

        // __________________________________PERIMETRE ___________________________________________

        //Recuperation de l id  du perimetre que le createur a souhaité donner au nouvel user
        $newUserPerimeter = $newUser[ 'perimeter' ] ;

        //Recuperation du repository lié a l entite perimetre et recuperation de l object perimetre souhaitant etre donné a l user depuis la base  a partie de l id renseigne dans le formulaire
        $perimeterRepo = $em->getRepository(Perimeter::class) ;
        $newUserPerimeterFromDb = $perimeterRepo->findOneById($newUserPerimeter) ;

        //Si on trouve pas de Perimetre lie a cet id en base on envoit une erreur
        if( ! $newUserPerimeterFromDb instanceof Perimeter ) throw new \Error('Impossible to recognize gived Perimeter') ;
//On recupere le perimetre du createur recupere de la base
        $creatorPerimeter = $userFromDb->getPerimeter();

        //Si le niveau perimetre du createur est superieur au niveau de celui qu il souhaite donner au nouvel utilisateur on envoit une erreur car il n a pas le droit de donner ce perimetre
        if($creatorPerimeter->getLevel() > $newUserPerimeterFromDb->getLevel() ) throw new \Error('You do not have the right to choose this perimeter') ;

        //Sinon on ajoute le nouveau perimetre a l user créé
        $newUserEntity->setPerimeter($newUserPerimeterFromDb) ;



       //---------------------------------ROLE----------------------------------//

        //Creation d'un tableau qui contient chaque role du createur  avec l enseigne auquel il appartient en guise de clé. Cela facilitera leur comparaison avec ceux que le createur souhaite donner à l'utilsiateur créé
        $userRolesWithCustomerIdAsKey= [] ;

        foreach($user->getRoles() as $customer){
            if( ! $customer instanceof Customer ) throw new \Error('Impossible to find Role Customer') ;
            $userRolesWithCustomerIdAsKey[$customer->getId()] = $customer;
        }

        //On boucle sur le tableau des roles souhaitant etre donné au nouvel utilisateur renseignés dans le formulaire
        foreach($newUser[ 'roles' ][ 'enseigne' ] as $roleEnseigneId => $roleIdInEnseigne) {

        //Si le createur n a pas de role pour l enseigne qu'il a renseigné dans le formulaire on envoit une erreur. Il ne peut donenr aucun role dans cette enseigne.
        if(! isset( $userRolesWithCustomerIdAsKey[ $roleEnseigneId ])) throw new \Error('Impossible to find  creator Role') ;

                //On stock l enseigne traité et on verifit qu elle est du bon type et qu elle contient bien un role
                $currentCustomer = $userRolesWithCustomerIdAsKey[ $roleEnseigneId ] ;

                if (! $currentCustomer  instanceof Customer || ! $currentCustomer->getRole() instanceof Role ) throw new \Error('Impossible to find  creator Role ') ;

                //On va chercher tous les managers et on test bien qu il existe un manager et donc une connection pour l enseigne traité.
                $managers =$this->getDoctrine()->getManagers();
                if(!  isset( $managers[ $currentCustomer->getName() ] ) ) throw new \Error('invalid connection') ;

                //On stock le manager gerant la conenction de l enseigne traité et on va chercher le repository de l entité role puis on recupere le role souhaitant etre donné à partir de l id renseigné dans le formulaire
                $currentEm = $managers[ $currentCustomer->getName() ];
                $roleRepo = $currentEm->getRepository(Role::class);
                $roleFromDb = $roleRepo->findOneById( $roleIdInEnseigne );

                //Si l id renseigné ne correspond a aucun  role en base on envoit une erreur.
                if( ! $roleFromDb instanceof Role ) throw new \Error('Impossible to find Role ') ;

                //On stock le niveau du role à donner recuperé depuis la base et celui du createur.
                $roleFromDbLevel = $roleFromDb->getLevel();
                $userCreatorRoleLevel = $currentCustomer->getRole()->getLevel() ;

                //On verifit bien que les niveaux sont du bon type et pas null, et que le createur a bien un niveau de role inferieur ou egal a celui qu'il souhaite donner sinon on envoit une erreur
                if ( ! is_int($roleFromDbLevel) ||  !is_int( $userCreatorRoleLevel ) || $userCreatorRoleLevel > $roleFromDbLevel ) throw new \Error('Impossible to set UserRole due to a creator level role problem ') ;
                $userRoleEntry = new UserRoles() ;

                //Si il n y a pas de problem on cree l entrée correspondante dans la table gerant les relations de chaque user  de la base admin vers leur role dans leur base customer.
                 $userRoleEntry->setRoleId( $roleFromDb->getId() ) ;
                 $userRoleEntry->setCustomer( $em->getRepository(Customer::class)->findOneById($currentCustomer->getId()) ) ;
                 $em->persist($userRoleEntry);
                 //dd($em);
            //On ajoute la relation à l entité user
            $newUserEntity->addUserRole($userRoleEntry);
        }



        //_______________________________________ SITES _________________________________________

        //Creation dun tableau contenant les sites possedé par le createur classé par enseignes afin de pouvoir effectuer une seule connection dans chaque  base enseigne pour la gestion des sites
        $userSitesWithCustomerIdAsKey= [] ;
        foreach($userFromDb->getSitesIds() as $siteEntry){
            $userSitesWithCustomerIdAsKey [ $siteEntry->getCustomer()->getId() ][$siteEntry->getSiteId()] = $siteEntry;
        }

        //On boucle sur les  sites que le  createur souhaite donner au nouvel users ( ceux ci sont classé par enseigne)
        foreach($newUser[ 'sites' ][ 'enseigne' ] as $siteEnseigneId => $siteIdInEnseigne) {
            //On verifit bien que l enseigne traité apparait bien dans le tableau des sites possedés par  le createur et que la valeur jointe est bien un tableau.
            if( isset( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) && is_array( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) ){

                //On recupere le tableau contenant les sites possedés par le createur
                $userSiteEntries = $userSitesWithCustomerIdAsKey[ $siteEnseigneId ] ;

               //On boucle sur le tableau des ids des sites souhaitant etre transmis a l user créé (renseignés dans le formulaire)
                foreach( $siteIdInEnseigne as $newUserSiteId) {
                    // On verifit bien que le createur possede bien le site qu 'il souhaite transmettre en verifiant que l id du site qu il veut donner est bien presant dans le tableau de ses sites
                    if(isset ( $userSiteEntries [ $newUserSiteId ]) ){
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
        }

        //On persiste l object contenant le nouvel user et on flush . l user est créé!!
        $em->persist($newUserEntity);
       // dd($em);
        $em->flush();

        return $this->redirectToRoute('user::create') ;
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