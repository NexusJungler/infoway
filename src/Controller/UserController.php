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


class UserController extends AbstractController
{
    private $passwordEncoder;
    private ObjectManager $__manager;
    private SessionManager $sessionManager ;

 public function __construct(UserPasswordEncoderInterface $passwordEncoder)
{
    $this->sessionManager =  new SessionManager( new Session() );
    $this->passwordEncoder = $passwordEncoder;
}


    /**
     * @Route(path="/users/create", name="user::create",methods="GET|POST")
     * @return Response
     */
    public function userCreate(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $givablesRoles = new \SplObjectStorage() ;
        $givableSites = [] ;
        $givablePerimeters = [] ;

        $user = $this->sessionManager->get('user');
        if( ! $user instanceof User ) {
            throw new \Error('Impossible to get User from Session');
        }

        $userPerimeterFromSession = $user->getPerimeter();

        if( ! $userPerimeterFromSession instanceof Perimeter ) {
            throw new \Error('Impossible to find User Perimeter in Session ');
        }

        $perimeterRepo = $em->getRepository(Perimeter::class);
        $userPerimeter = $perimeterRepo->findOneById($userPerimeterFromSession->getId());
        if( ! $userPerimeter instanceof Perimeter ) {
            throw new \Error('Impossible to find User Perimeter  ');
        }

        $givablePerimeters = $perimeterRepo ->findPerimeterByLevelEqualOrBellow( $userPerimeter->getLevel() );


        $userRoles = $user->getRoles();

        foreach( $userRoles as $userRoleByCustomer ) {
            if( $userRoleByCustomer instanceof Customer ) {
                $customerRole = $userRoleByCustomer->getRole() ;
                if( $customerRole instanceof Role ) {
                    $customererManager = $this->getDoctrine()->getManager( $userRoleByCustomer->getName() );
                    $roleRepo = $customererManager->getRepository(Role::class);

                    $givableRolesFrombase  = $roleRepo->getRolesByLevelBellow($customerRole->getLevel());
                    if( is_array($givableRolesFrombase) ) $givablesRoles[ $userRoleByCustomer ] = $givableRolesFrombase ;

                }
            }
        }
       $userSites = $user->getSites() ;

        $givableSites = $userSites ;

        return $this->render('user/create.html.twig', [
            'givablePerimeters' => $givablePerimeters ,
            'givableSites'   => $givableSites ,
            'givableRoles'   => $givablesRoles
        ]) ;

    }

    /**
     * @Route(path="/users/create/process", name="users::create::process",methods="POST")
     * @return Response
     */
    public function userCreateProcess(Request $request): Response
    {


        $newUser = $request->get('user');

        dump($newUser) ;

        if(! isset( $newUser[ 'roles' ] ) || !isset( $newUser[ 'roles' ][ 'enseigne' ] ) || !is_array( $newUser[ 'roles' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Role Entry ');
        if(! isset( $newUser[ 'sites' ] ) || !isset( $newUser[ 'sites' ][ 'enseigne' ] ) || !is_array( $newUser[ 'sites' ][ 'enseigne' ] )) throw new \Error('Invalid argument for Site Entry ');
        if(! isset( $newUser[ 'perimeter' ] ) ) throw new \Error('Invalid argument for Perimeter ');
        if(! isset( $newUser[ 'email' ] ) ) throw new \Error('Invalid argument for email ');
        if(! isset( $newUser[ 'phone' ] ) ) throw new \Error('Invalid argument for phone ');
        if(! isset( $newUser[ 'fristname' ] ) ) throw new \Error('Invalid argument for firstname ');
        if(! isset( $newUser[ 'lastname' ] ) ) throw new \Error('Invalid argument for lastname ');

        $user = $this->sessionManager->get('user');

        $em = $this->getDoctrine()->getManager('default');

        $userRepo = $em->getRepository(User::class) ;

        $userFromDb = $userRepo->findOneById($user->getId()) ;


        if( ! $userFromDb instanceof User) throw new \Error('Impossible to get User Informations') ;

        $newUserEntity = new User() ;

        $newUserEntity->setFirstName( $newUser[ 'fristname' ] );
        $newUserEntity->setLastName($newUser['lastname']) ;
        $newUserEntity->setEmail($newUser[ 'email' ] ) ;
        $newUserEntity->setPhoneNumber($newUser[ 'phone' ]);

        $newUserEntity->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'test'
        ));
        $newUserPerimeter = $newUser[ 'perimeter' ] ;

        $perimeterRepo = $em->getRepository(Perimeter::class) ;

        $newUserPerimeterFromDb = $perimeterRepo->findOneById($newUserPerimeter) ;

        if( ! $newUserPerimeterFromDb instanceof Perimeter ) throw new \Error('Impossible to recognize gived Perimeter') ;

        $creatorPerimeter = $userFromDb->getPerimeter();

        if($creatorPerimeter->getLevel() > $newUserPerimeterFromDb->getLevel() ) throw new \Error('You do not have the right to choose this perimeter') ;

        $newUserEntity->setPerimeter($newUserPerimeterFromDb) ;



       //---------------------------------ROLE----------------------------------//
        $userRolesWithCustomerIdAsKey= [] ;

        foreach($user->getRoles() as $customer){
            if( ! $customer instanceof Customer ) throw new \Error('Impossible to find Role Customer') ;
            $userRolesWithCustomerIdAsKey[$customer->getId()] = $customer;
        }

        foreach($newUser[ 'roles' ][ 'enseigne' ] as $roleEnseigneId => $roleIdInEnseigne) {


        if(! isset( $userRolesWithCustomerIdAsKey[ $roleEnseigneId ])) throw new \Error('Impossible to find  creator Role') ;

                $currentCustomer = $userRolesWithCustomerIdAsKey[ $roleEnseigneId ] ;

                if (! $currentCustomer  instanceof Customer || ! $currentCustomer->getRole() instanceof Role ) throw new \Error('Impossible to find  creator Role ') ;

                $managers =$this->getDoctrine()->getManagers();
                if(!  isset( $managers[ $currentCustomer->getName() ] ) ) throw new \Error('invalid connection') ;

                $currentEm = $managers[ $currentCustomer->getName() ];
                $roleRepo = $currentEm->getRepository(Role::class);
                $roleFromDb = $roleRepo->findOneById( $currentCustomer->getRole()->getId() );

                if( ! $roleFromDb instanceof Role ) throw new \Error('Impossible to find Role ') ;

                $roleFromDbLevel = $roleFromDb->getLevel();
                $userCreatorRoleLevel = $currentCustomer->getRole()->getLevel() ;

                if ( ! is_int($roleFromDbLevel) ||  !is_int( $userCreatorRoleLevel ) || $userCreatorRoleLevel > $roleFromDbLevel ) throw new \Error('Impossible to set UserRole due to a creator level role problem ') ;
                $userRoleEntry = new UserRoles() ;

                 $userRoleEntry->setRoleId( $roleFromDb->getId() ) ;
                 $userRoleEntry->setCustomer( $em->getRepository(Customer::class)->findOneById($currentCustomer->getId()) ) ;
                 $em->persist($userRoleEntry);
                 //dd($em);
            dump($em);
            $newUserEntity->addUserRole($userRoleEntry);
        }

        $userSitesWithCustomerIdAsKey= [] ;
        foreach($userFromDb->getSitesIds() as $siteEntry){
            $userSitesWithCustomerIdAsKey [ $siteEntry->getCustomer()->getId() ][$siteEntry->getSiteId()] = $siteEntry;
        }

        foreach($newUser[ 'sites' ][ 'enseigne' ] as $siteEnseigneId => $siteIdInEnseigne) {
            if( isset( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) && is_array( $userSitesWithCustomerIdAsKey[ $siteEnseigneId ]  ) ){

                $userSiteEntries = $userSitesWithCustomerIdAsKey[ $siteEnseigneId ] ;

                //dd($userSiteEntries);
                foreach( $siteIdInEnseigne as $newUserSiteId) {

                    if(isset ( $userSiteEntries [ $newUserSiteId ]) ){
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

        $em->persist($newUserEntity);
       // dd($em);
        $em->flush();

        return $this->redirectToRoute('user::create') ;
    }

//    /**
//     * @Route(path="/login", name="user::login",methods="GET|POST")
//     * @param AuthenticationUtils $authenticationUtils
//     * @param TranslatorInterface $translator
//     * @return Response
//     */
//    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
//    {
//
//        if ($this->getUser()) {
//             return $this->redirectToRoute('app::home');
//         }
//
//        // get the login error if there is one
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//
//        $error = $authenticationUtils->getLastAuthenticationError();
//
//        if(!is_null($error))
//        {
//            //translate
//            $error = $translator->trans($error->getMessageKey());
//        }
//
//        dump($error);
//
//        return $this->render('security/login.html.twig', [
//            'last_username' => $lastUsername,
//            'message' => $error
//        ]);
//    }


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


    /**
     * @Route(path="/send/password/reset/email", name="user::send_password_reset_email", methods="POST")
     *
     * @param Request $request
     * @param EmailSenderService $mailer
     * @return Response
     * @throws Exception
     */
	public function sendPasswordResetEmail(Request $request, EmailSenderService $mailer, TokenGeneratorService $tokenGeneratorService): Response
    {

        if(is_null($request->request->get('username')))
        {
            throw new Exception("Missing 'username' parameter !");
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneByUsername($request->request->get('username'));

        if (!$user) {
            throw new Exception("User not found !");
        }

        $token = $tokenGeneratorService->generate(64);

        $userEmail = $user->getEmail();

        $user->setUserPassword("")
             ->setPasswordResetToken($token);

        // maj de la base de donnée
        $this->getDoctrine()->getManager()->flush();


        $mailer->sendEmail(
                        "cbaby@infoway.fr", $userEmail,
                        "cbaby@infoway.fr", "Reset my password", $this->renderView(
                        "security/resetPasswordEmail.html.twig", [
                        'token' => $token,
                        'username' => strtoupper($user->getUsername())
                        ]
                    ), 'text/html'
                );

        // message flash dans la session
        (new Session())->getFlashBag()->add("message", "Merci de vérifier votre boîte mail, un mail contenant le lien pour réinitialiser votre mot de passe vous a été envoyé !");

        return $this->redirectToRoute("user::password_forget");

    }


    /**
     * Renvoie une vue permettant de reinitialisser son mot de passe
     *
     * @Route(path="/reset/password/{password_reset_token}", name="user::reset_password", methods="GET|POST")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param User $user
     * @return Response
     * @throws Exception
     */
	public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {

        if($request->isMethod("POST"))
        {
            if(!empty($request->request->get("new_password")))
            {

                // on rajoute le token dans le champ "password_reset_token"
                $user->setPassword($passwordEncoder->encodePassword($user, $request->get("new_password")));

                // le token a été utilisé, on peut le supprimer
                $user->setPasswordResetToken("");

                // on mets à jour la base de donnée
                $this->getDoctrine()->getManager()->flush();

                // message flash dans la session
                (new Session())->getFlashBag()->add("message", "Votre mot de passe a bien été modifié ! Vous pouvez vous connecter <a href='". $this->generateUrl("user::login") ."'>içi</a>");
            }
            else
            {
                throw new Exception("Missing new_password parameter !");
            }
        }

        return $this->render("security/resetPassword.html.twig");
      
    }

    /**
     * @Route(path="/registration/confirm/{registration_token}", name="user::registration_confirmation", methods="GET|POST")
     *
     * @param User $user
     * @return Response
     */
     public function registrationConfirm(User $user): Response
     {

         // uncomment this if you want delete registration token on account confirmation
        /*$user->setRegistrationToken("");

        $this->getDoctrine()->getManager()->flush();*/

        return $this->render("security/confirmInscription.html.twig");

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


}