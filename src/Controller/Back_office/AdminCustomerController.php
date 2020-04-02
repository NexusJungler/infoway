<?php


namespace App\Controller\Back_office;


use App\Entity\Customer\Country;
use App\Entity\Admin\Customer;
use App\Entity\Customer\CustomerSearch;
use App\Entity\Admin\Role;
use App\Entity\Customer\SalarySearch;
use App\Entity\Customer\Site;
use App\Entity\Customer\SiteSearch;
use App\Entity\Admin\User;
use App\Form\CreateCompanyPieceType;
use App\Form\CustomerSearchType;
use App\Form\CustomerType;
use App\Form\SalarySearchType;
use App\Form\SiteSearchType;
use App\Form\SiteType;
use App\Form\UserType;
use App\Service\EmailSenderService;
use App\Service\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(path="/back-office")
 */
class AdminCustomerController extends AbstractController
{


    /**
     * @Route(path="/show/all/customers", name="admin_customer::showAll", methods="GET")
     * @param Request $request
     * @param PaginatorService $paginator
     * @return Response
     */
    public function showAll(Request $request, PaginatorService $paginator): Response
    {




        $search = new CustomerSearch();
        $form = $this->createForm(CustomerSearchType::class, $search);
        $form->handleRequest($request);

        $customers = $paginator->paginate(
            $this->getDoctrine()->getRepository(Customer::class)->paginate($search),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);
        $numberOfCustomers = $customers->getTotalItemCount();

        return $this->render("back-office/customer/showAll.html.twig", [
            'customers' => $customers,
            'form' => $form->createView(),
            'numberOfCustomers' => $numberOfCustomers
        ]);

    }


    /**
     * @Route(path="/create/customer", name="admin_customer::create", methods="POST|GET")
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->persist($customer);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("message", "Entreprise crée !");

            return $this->redirectToRoute("admin_customer::show", [
                'id' => $customer->getId(),
                'name' => $customer->getName()
            ]);

        }

        return $this->render("back-office/customer/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Ajouter une entreprise"
        ]);

    }


    /**
     * @Route(path="/show/customer/{id}-{name}", name="admin_customer::show", methods="GET")
     *
     * @param Customer $customer
     * @return Response
     */
    public function show(Customer $customer): Response
    {

        $form = $this->createForm(CustomerType::class, $customer);

        return $this->render("back-office/customer/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Voir une entreprise",
            'customer' => $customer,
        ]);

    }


    /**
     * @Route(path="/edit/customer/{id}-{name}", name="admin_customer::edit", methods="POST|GET")
     *
     * @param Customer $customer
     * @param Request $request
     * @return Response
     */
    public function edit(Customer $customer, Request $request): Response
    {

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("message", "Entreprise modifiée !");

            return $this->redirectToRoute("admin_customer::show", [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
            ]);

        }

        return $this->render("back-office/customer/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Editer une entreprise"
        ]);

    }


    /**
     * @Route(path="/delete/customer/{id}-{name}", name="admin_customer::delete", methods="DELETE")
     *
     * @param Customer $customer
     * @param Request $request
     * @return Response
     */
    public function delete(Customer $customer, Request $request): Response
    {
        if($this->isCsrfTokenValid("delete". $customer->getId(), $request->get("_token")))
        {
            $this->getDoctrine()->getManager()->remove($customer);
            $this->getDoctrine()->getManager()->flush();
        }
        else
        {
            $this->addFlash("error", "Token CSRF non valide !");
        }

        return $this->redirectToRoute('admin_customer::showAll');
    }


    /**
     * @Route(path="/show/customer/{id}-{name}/salaries", name="admin_customer::showSalaries", methods="GET")
     *
     * @param Customer $customer
     * @param Request $request
     * @param PaginatorService $paginator
     * @return Response
     */
    public function showCustomerSalaries(Customer $customer, Request $request, PaginatorService $paginator): Response
    {
        $search = new SalarySearch();
        $form = $this->createForm(SalarySearchType::class, $search);
        $form->handleRequest($request);

        $salaries = $paginator->paginate(
            $this->getDoctrine()->getRepository(User::class)->paginate($customer, $search),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);
        $numberOfSalaries = $salaries->getTotalItemCount();

        return $this->render("back-office/customer/show_salaries.html.twig", [
            'salaries' => $salaries,
            'customer' => $customer,
            'numberOfSalaries' => $numberOfSalaries,
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route(path="/customer/{id}-{name}/add/salary", name="admin_customer::addSalary", methods="GET|POST")
     *
     * @param Customer $customer
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EmailSenderService $emailSenderHelper
     * @return Response
     * @throws \Exception
     */
    public function addSalary(Customer $customer, Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailSenderService $emailSenderHelper): Response
    {

        $user = new User();
        $user->setCustomer($customer)
             ->setRole( $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'User']) );

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $token = bin2hex(openssl_random_pseudo_bytes(64));

            $site = $this->getDoctrine()->getRepository(Site::class)->findOneBy(['id' => $request->request->get('user_registration_form')['site']]);

            $user->setCustomer($customer)
                 ->setPassword($passwordEncoder->encodePassword($user, $user->getUserPassword()))
                 ->setRegistrationToken($token)
                 ->setRegistrationDate(new \DateTime())
                 ->setRegistrationIsConfirmed(false)
                 ->setSite($site);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $emailSenderHelper->sendEmail("cbaby@infoway.fr", $user->getEmail(),
                                          "cbaby@infoway.fr", "Registration confirmation", $this->renderView(
                    "security/confirmInscriptionEmail.html.twig", [
                    'username' => $user->getUsername(),
                    'token' => $token
                ]), 'text/html');

            $this->addFlash('message', "Merci de vérifier votre boite mail, un mail contenant un lien de confirmation vient de vous etre envoyé ! Merci d'utiliser ce lien pour confirmer cotre inscription ");

        }

        return $this->render('back-office/user/create_show_edit.html.twig', [
            'form' => $form->createView(),
            'sites' => $customer->getSites()->getValues(),
            'title' => "Créer un utilisateur",
        ]);

    }


    /**
     * @Route(path="/show/{id}-{name}/sites", name="admin_customer::showSites", methods="GET")
     *
     * @param Customer $customer
     * @param Request $request
     * @param PaginatorService $paginator
     * @return Response
     */
    public function showCustomerSites(Customer $customer, Request $request, PaginatorService $paginator): Response
    {
        $search = new SiteSearch();
        $form = $this->createForm(SiteSearchType::class, $search);
        $form->handleRequest($request);

        $sites = $paginator->paginate(
            $this->getDoctrine()->getRepository(Site::class)->paginate($customer, $search),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/);
        $numberOfSalaries = $sites->getTotalItemCount();

        return $this->render("back-office/customer/show_sites.html.twig", [
            'sites' => $sites,
            'customer' => $customer,
            'numberOfSalaries' => $numberOfSalaries,
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route(path="/customer/{id}-{name}/create/site", name="admin_customer::addSite", methods="GET|POST")
     *
     * @param Customer $customer
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addSite(Customer $customer, Request $request): Response
    {

        $site = new Site();
        $site->setCustomer($customer);

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $site->setCustomer($customer);

            $customer->addSite($site);
            $this->getDoctrine()->getRepository(Country::class)->findOneBy(['name' => $site->getCountry()->getName()])->addSite($site);


            $this->getDoctrine()->getManager()->persist($site);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("admin_customer::show", [
                'id' => $customer->getId(),
                'name' => $customer->getName()
            ]);

        }

        return $this->render('back-office/site/create_show_edit.html.twig', [
            'title' => "Créer un site",
            'form' => $form->createView(),
            'action' => 'Créer'
        ]);
    }



}