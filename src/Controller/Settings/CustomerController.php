<?php

namespace App\Controller\Settings;

use App\Entity\Admin\Customer;
use App\Form\Admin\CustomerType;
use App\Repository\Admin\CustomerRepository;
use App\Service\ContactHandlerService;
use App\Service\CustomerHandlerService;
use App\Service\DatabaseAccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customers_index", methods={"GET"})
     */
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('settings/customers/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customers_new", methods={"GET","POST"})
     */
    public function new(Request $request, CustomerHandlerService $customerHandler, DatabaseAccessHandler $databaseHandler): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if  (   !$customerHandler->areCustomerDatasValid($customer)
                ||  $customerHandler->isCustomerWithNameEnteredAlreadyExist($customer)
                ||  !$customerHandler->handleCustomerLogoFromForm( $customer, $form )
                ||  !$customerHandler->createCustomerDatabase( $customer )
                ||  !$customerHandler->insertCustomerInDb($customer)

                )   return $this->redirectToRoute('customers_new');


            return $this->redirectToRoute('customers_index');
        }

        return $this->render('settings/customers/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customers_show", methods={"GET"})
     */
    public function show(Customer $customer): Response
    {
        return $this->render('settings/customers/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Customer $customer): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customers_index');
        }

        return $this->render('settings/customers/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customers_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Customer $customer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customers_index');
    }
}
