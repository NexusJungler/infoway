<?php


namespace App\Controller;


use App\Entity\Admin\Action;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\Permission;
use App\Entity\Admin\Role;
use App\Entity\Admin\Subject;
use App\Entity\Admin\User;
use App\Entity\Admin\UserRoles;
use App\Entity\Admin\UserSites;
use App\Form\UserType;
use App\Repository\Admin\UserRepository;
use App\Service\EmailSenderService;
use App\Service\EmailVerificator;
use App\Service\PermissionsHandler;
use App\Service\SessionManager;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;


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
     * @Route(path="/login", name="user::login",methods="GET|POST")
     * @param AuthenticationUtils $authenticationUtils
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('app::home');
         }

        // get the login error if there is one
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $error = $authenticationUtils->getLastAuthenticationError();

        if(!is_null($error))
        {
            //translate
            $error = $translator->trans($error->getMessageKey());
        }

        dump($error);

        return $this->render('user/user.login.html.twig', [
            'page_title' => 'Connexion',
            'form_title' => 'Connexion',
            'last_username' => $lastUsername,
            'message' => $error
        ]);
    }


    /**
     * @Route("path=/logout", name="user::logout")
     * This method can be blank - it will be intercepted by the logout key on your firewall (see : config/packages/security.yaml)
     */
    public function logout() { }


    /**
     *
     * @Route(path="/password/forget", name="user::passwordForget", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
	public function passwordForget(Request $request, EmailVerificator $emailVerificator, EmailSenderService $emailSenderService, UserRepository $userRepository): Response
    {

        if($request->isMethod('POST'))
        {

            $emailNotFound = false;
            $emailIsSend = false;

            if(!is_null($request->request->get('user')))
            {

                $userInfo = $request->request->get('user');

                if(array_key_exists('email', $userInfo) AND $emailVerificator->isValidEmail($userInfo['email']))
                {
                    $user = $userRepository->findOneByEmail($userInfo['email']);

                    if($user)
                    {
                        //$this->sendPasswordResetEmail($emailSenderService, $user);
                        $emailIsSend = true;
                    }
                    else
                        $emailNotFound = true;

                    dump($userInfo, $user);
                }
                else
                    $emailNotFound = true;

            }
            else
                $emailNotFound = true;

            $error = ($emailNotFound === true) ? "Cette adresse email n'est pas valide" : null;

        }

        return $this->render("user/user.login.html.twig", [
            'page_title' => 'Mot de passe oublié',
            'form_title' => 'Mot de passe oublié ?',
            'form_subtitle' => 'Veuillez renseignez votre email',
            'emailIsSend' => $emailIsSend ?? false,
            'error' => $error ?? null
        ]);
    }


    /**
     * Renvoie une vue permettant de reinitialisser son mot de passe
     *
     * @Route(path="/reset/password/{password_reset_token}", name="user::resetPassword", methods="GET|POST")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EmailSenderService $emailSenderService
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param string $password_reset_token
     * @return Response
     * @throws Exception
     */
	public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailSenderService $emailSenderService, EntityManagerInterface $entityManager, UserRepository $userRepository, string $password_reset_token): Response
    {

        $user = $userRepository->findOneByPasswordResetToken($password_reset_token);

        if($request->isMethod('POST'))
        {

            $userInfo = $request->request->get('user');
            //dd($userInfo);

            if($userInfo['password'] !== $userInfo['password_confirm'])
            {
                $error = "Les 2 mots de passe doivent être identique !";
            }

            // if password don't respect this rule : Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character
            elseif(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $userInfo['password']))
            {
                $error = "Le mot de passe doit contenir minimum 8 caractères, 1 minuscules, 1 majuscules, 1 chiffres et 1 caractère speciale !";
            }

            else
            {

                $this->updateUserPassword($user,$userInfo['password'], $passwordEncoder, true);

                $entityManager->flush();

                $passwordWasUpdated = true;
                //$this->addFlash('message', 'Votre mot de passe a bien été modifié avec succés !');
            }

        }
        else
        {

            // token not found or token is not valid
            // send a new email which will contain new token
            if(!$user OR !$this->userPasswordResetTokenIsValid($user))
            {
                $tokenIsInvalid = true;

                /*if($user)
                    $this->sendPasswordResetEmail($emailSenderService, $user);*/

               // else
                if(!$user)
                    $userNotFound = true;

            }

            dump($user);

        }

        return $this->render('user/user.login.html.twig', [
            'page_title' => 'Réinitialisation du Mot de passe',
            'form_title' => 'Réinitialisation du mot de passe',
            'error' => $error ?? null,
            'tokenIsInvalid' => $tokenIsInvalid ?? false,
            'passwordWasUpdated' => $passwordWasUpdated ?? false,
            'userNotFound' => $userNotFound ?? null
        ]);
      
    }


    /**
     * @Route(path="/users/list", name="user::showAllUsers")
     * @return Response
     */
    public function showAllUsers()
    {

        $this->__manager = $this->getDoctrine()->getManager('default');

        return $this->render(
            'user/user.showAll.html.twig', [
            'users' => $this->__manager->getRepository(User::class)->findAll()
        ]);

    }


    /**
     * @Route(path="/user/{id}/permissions", name="user::showUserPermissions")
     * @return Response
     */
    public function showUserPermissions(User $user)
    {

        $this->__manager = $this->getDoctrine()->getManager('default');

        $permissionsHandler = new PermissionsHandler($this->__manager);

        $userPermissions = $permissionsHandler->getUserPermissions($user, true);
        $userRolePermissions = $permissionsHandler->getUserRolePermissions($user, false);

        $actions = $this->__manager->getRepository(Action::class)->findAll();

        //dd($userRolePermissions, $userPermissions);

        dump($userRolePermissions, $userPermissions);

        return $this->render('user/user.editPermissions.html.twig', [
            'user' => (object) ['id' => $user->getId(), 'username' => $user->getUsername(), 'role' => $user->getRole()->getName()],
            'userPermissions' => $userPermissions,
            'rolePermissions' => $userRolePermissions,
            'actions' => $actions
        ]);

    }


    /**
     * @Route(path="/edit/user/{id}/permissions", name="user::editUserPermissions", methods="POST")
     */
    public function editUserPermissions(User $user, Request $request)
    {

        if($request->request->get('permissions') === null)
            throw new Exception(sprintf("Internal Error : Missing or invalid '%s' parameter !",'permissions'));

        $permissions = json_decode($request->request->get('permissions'));

        // before update
        //dump(sizeof($user->getPermissions()->getValues()));

        $updateState = $this->updateUserPermissions($user, $permissions);

        // after update
        //dd(sizeof($user->getPermissions()->getValues()));

        return new JsonResponse([
            'status' => 200,
            'message' => "200 OK"
        ]);

    }


    /**
     * @Route(path="/create/user", name="user::createUser", methods={"POST"})
     * @param Request $request
     * @param TokenGeneratorService $tokenGeneratorService
     * @param EmailVerificator $emailVerificator
     * @param EmailSenderService $emailSenderService
     * @throws Exception
     */
    public function createUser(Request $request, TokenGeneratorService $tokenGeneratorService, EmailVerificator $emailVerificator, EmailSenderService $emailSenderService)
    {

        $userInfo = $request->request->get('user');

        dd($request->request);

        // verification des données et insertions de l'utilisateurs en bdd

        if(array_key_exists('email', $userInfo) AND $emailVerificator->isValidEmail($userInfo['email']))
        {

            $emailSenderService->sendEmail($userInfo['email'], 'Inscription Confirmation',$this->renderView(
                "emails/user.accountConfirmation.twig", [
                'account_confirmation_token' => $tokenGeneratorService->generate(64),
                'user' => $userInfo['lastname'] . ' ' . $userInfo['fristname']
            ]),'text/html');


            // meessage flash de confirmation de création de l'utilisateur ( et envoie de l'email ?)
            //$this->addFlash('message', "");


        }
        else
            throw new \Exception(sprintf("Internal Error : user email is not valid or not found !"));

        dd($request->request->get('user'));

    }


    /**
     * @Route(path="/user/account/confirmation/{account_confirmation_token}", name="user::accountConfirmation", methods={"GET", "POST"},
     *     requirements={"account_confirmation_token": "\w+"})
     */
    public function accountConfirmation(Request $request, string $account_confirmation_token, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {

        $user = $userRepository->findOneByAccountConfirmationToken($account_confirmation_token);

        if($request->isMethod('POST') AND $user)
        {

            $userInfo = $request->request->get('user');

            if($userInfo['password'] !== $userInfo['password_confirm'])
            {
                $this->addFlash('message', 'Les 2 mots de passe doivent être identique !');
            }
            else
            {

                $this->updateUserPassword($user, $userInfo['password'], $passwordEncoder);

                $user->setAccountConfirmedAt(new \DateTime())
                     ->setAccountConfirmationToken(null)
                     ->setActivated(true);

                $entityManager->flush();

                $this->addFlash('message', 'Votre mot de passe a bien été modifié avec succés !');

            }

        }
        else
        {

            if(!$user OR $user->getActivated() === true)
            {
                $error = true;
            }

        }

        return $this->render('user/user.accountConfirmation.twig', [
            'error' => $error ?? false
        ]);

    }


    /**
     * @param User $user
     * @param array $permissions
     * @return bool
     */
    private function updateUserPermissions(User $user, array $permissions)
    {

        $userPermissionsDefaultSize = sizeof($user->getPermissions()->getValues());

        foreach ($permissions as $permission_json)
        {

            $permission = $this->getDoctrine()->getManager('default')->getRepository(Permission::class)->findOneById($permission_json->__id);

            if(!$permission_json->__state AND $user->getPermissions()->contains($permission))
                $user->removePermission($permission);

            elseif ($permission_json->__state AND !$user->getPermissions()->contains($permission))
                $user->addPermission($permission);

        }

        if(($userPermissionsDefaultSize !== $user->getPermissions()->getValues()) )
            $this->getDoctrine()->getManager('default')->flush();

        return true;

    }


    /**
     * @param User $user
     * @return bool
     * @throws Exception
     */
    private function userPasswordResetTokenIsValid(User $user): bool
    {
        $date_diff = intval( (date_diff($user->getRequestedPasswordAt(), new \DateTime()))->format('%h') );

        return $date_diff <= intval($this->getParameter('passwordResetTokenTimer'));
    }


    /**
     * @param EmailSenderService $emailSenderService
     * @param User $user
     * @throws Exception
     */
    private function sendPasswordResetEmail(EmailSenderService $emailSenderService, User $user)
    {

        $tokenGenerator = new TokenGeneratorService();
        $token = $tokenGenerator->generate(64);

        $user->setPasswordResetToken($token)
            ->setRequestedPasswordAt(new \DateTime());

        $this->getDoctrine()->getManager()->flush();

        $emailSenderService->sendEmail($user->getEmail(), 'Modification du mot de passe' ,$this->renderView(
            "emails/user.resetPassword.twig", [
            'password_reset_token' => $token,
            'user' => $user->getLastName() . ' ' . $user->getFirstName()
        ]),'text/html');

    }


    /**
     * @param User $user
     * @param string $password
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param bool $isPasswordForget
     */
    private function updateUserPassword(User $user, string $password, UserPasswordEncoderInterface $passwordEncoder, bool $isPasswordForget = false)
    {

        $user->setPassword($passwordEncoder->encodePassword($user, $password));

        if($isPasswordForget)
            $user->setPasswordResetToken(null);

        else
            $user->setAccountConfirmationToken(null);

        //$this->getDoctrine()->getManager('default')->flush();

        //$this->addFlash('message', 'Votre mot de passe a bien été modifié avec succés !');

    }


}