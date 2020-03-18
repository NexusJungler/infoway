<?php


namespace App\Controller;



use App\Entity\Admin\{ Action AS Admin_Action, Country AS Admin_Country, Customer AS Admin_Customer,
    Feature AS Admin_Feature, Permission AS Admin_Permission, Role AS Admin_Role,
    Subject AS Admin_Subject, TimeZone AS Admin_TimeZone, User AS Admin_User };
use App\Form\UserType;
use App\Service\{EmailSenderService, PermissionsHandler, TokenGeneratorService};
use Doctrine\Persistence\ObjectManager;
use \Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;


class UserController extends AbstractController
{


    /**
     * @var ObjectManager
     */
    private ObjectManager $__manager;

    public function __construct()
    {

    }


    /**
     * @Route(path="/login", name="user::login",methods="GET|POST")
     * @param AuthenticationUtils $authenticationUtils
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {

        die();
        /*if ($this->getUser()) {
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

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'message' => $error
        ]);*/
    }

    /**
     * @Route(path="/register", name="user::register", methods="GET|POST")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EmailSenderService $emailSenderHelper
     * @return Response
     * @throws Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailSenderService $emailSenderHelper): Response
    {

        die();
/*        $user = new Admin_User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $token = bin2hex(openssl_random_pseudo_bytes(64));

            $user->setPassword($passwordEncoder->encodePassword($user, $user->getUserPassword()))
                 ->setRegistrationToken($token)
                 ->setRegistrationDate(new \DateTime())
                 ->setRegistrationIsConfirmed(false);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $emailSenderHelper->sendEmail("cbaby@infoway.fr", $user->getEmail(),
                                          "cbaby@infoway.fr", "Registration confirmation", $this->renderView(
                    "security/confirmInscriptionEmail.html.twig", [
                    'username' => $user->getUsername(),
                    'token' => $token
                ]), 'text/html');


            $this->addFlash('message', "Merci de vérifier votre boite mail, un mail contenant un lien de confirmation vient de vous etre envoyé ! Merci d'utiliser ce lien pour confirmer cotre inscription ");

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app::register');
        }

        return $this->render('back-office/user/create_show_edit.html.twig', [
            'form' => $form->createView(),
        ]);*/

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


    /**
     * @Route(path="/send/password/reset/email", name="user::send_password_reset_email", methods="POST")
     *
     * @param Request $request
     * @param EmailSenderService $mailer
     * @return Response
     * @throws Exception
     */
	public function sendPasswordResetEmail(Request $request, EmailSenderService $mailer): Response
    {

        die();
/*        if(is_null($request->request->get('username')))
        {
            throw new Exception("Missing 'username' parameter !");
        }

        $user = $this->getDoctrine()->getRepository(User::class)->checkIfUserExist(["username" => $request->request->get('username')]);

        if (!$user) {
            throw new Exception("User not found !");
        }

        // openssl_random_pseudo_bytes generate a pseudo-random string of bytes using length
        // openssl_random_pseudo_bytes(int $length)
        // https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
        //
        // bin2hex convert binary data into hexadecimal
        // bin2hex(string $binaryData)
        // https://www.php.net/manual/en/function.bin2hex.php
        $token = bin2hex(openssl_random_pseudo_bytes(64));

        // recupère l'email saisie via le formulaire
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

        return $this->redirectToRoute("password_forget");*/

    }


    /**
     * Renvoie une vue permettant de reinitialisser son mot de passe
     *
     * @Route(path="/reset/password/{password_reset_token}", name="user::reset_password", methods="GET|POST")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Admin_User $user
     * @return Response
     * @throws Exception
     */
	public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, Admin_User $user): Response
    {

/*        if($request->isMethod("POST"))
        {
            if(!empty($request->request->get("new_password")))
            {

                // on rajoute le token dans le champ "password_reset_token"
                $user->setUserPassword($passwordEncoder->encodePassword($user, $request->get("new_password")));

                // on recupère la date actuelle on l'enregistre
                $user->setPasswordResetDate(new \DateTime());

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

        return $this->render("security/resetPassword.html.twig");*/

    die();
      
    }

    /**
     * @Route(path="/registration/confirm/{registration_token}", name="user::registration_confirmation", methods="GET|POST")
     *
     * @param Admin_User $user
     * @return Response
     */
     public function registrationConfirm(Admin_User $user): Response
     {

        /*$user->setRegistrationToken("")
             ->setRegistrationIsConfirmed(true);

        $this->getDoctrine()->getManager()->flush();

        return $this->render("security/confirmInscription.html.twig");*/
        die();

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
            'users' => $this->__manager->getRepository(Admin_User::class)->findAll()
        ]);

    }


    /**
     * @Route(path="/user/{id}/permissions", name="user::showUserPermissions")
     * @return Response
     */
    public function showUserPermissions(Admin_User $user)
    {

        $this->__manager = $this->getDoctrine()->getManager('default');

        $permissionsHandler = new PermissionsHandler($this->__manager);

        $userPermissions = $permissionsHandler->getUserPermissions($user, true);
        $userRolePermissions = $permissionsHandler->getUserRolePermissions($user, false);

        $actions = $this->__manager->getRepository(Admin_Action::class)->findAll();

        //dd($userRolePermissions, $userPermissions);

        dump($user, $userPermissions);

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
    public function editUserPermissions(Admin_User $user, Request $request)
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


    private function updateUserPermissions(Admin_User $user, array $permissions)
    {

        $userPermissionsDefaultSize = sizeof($user->getPermissions()->getValues());

        foreach ($permissions as $permission_json)
        {

            $permission = $this->getDoctrine()->getManager('default')->getRepository(Admin_Permission::class)->findOneById($permission_json->__id);

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