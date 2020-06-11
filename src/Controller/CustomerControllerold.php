<?php


namespace App\Controller;


use App\Form\CustomerCreationType;
use App\Repository\Admin\UserRepository;
use App\Service\{ArraySearchRecursiveService, DatabaseAccessHandler, PermissionsHandler};
use App\Entity\Admin\{Contact, Customer};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\Exception\FileException, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class CustomerControllerold extends AbstractController
{

    /**
     * @Route(path="/create/customer", name="customer::createCustomer", methods={"GET", "POST"})
     * @param Request $request
     * @param DatabaseAccessHandler $databaseHandler
     * @param PermissionsHandler $permissionsHandler
     * @return Response
     * @throws \Exception
     */
    public function createCustomer(Request $request, DatabaseAccessHandler $databaseHandler, PermissionsHandler $permissionsHandler, UserRepository $userRepository): Response
    {

        $customer = new Customer();
        $form = $this->createForm(CustomerCreationType::class, $customer);
        $form->handleRequest($request);

        $adminEm = $this->getDoctrine()->getManager('default');
        $error = false;

        if($form->isSubmitted() AND $form->isValid())
        {

            dd($form);
            $logoFile = $form->get('logoFile')->getData();

            if($logoFile)
            {

                $newFileName = $customer->getName().'.'.$logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('logoDirectory'),
                        $newFileName
                    );
                }
                catch (FileException $e) {
//                    dd($e->getMessage());
                }
                $customer->setLogo($newFileName);

            }

            if(!$databaseHandler->databaseExist($customer->getName()))
            {
                if($databaseHandler->createDatabase($customer->getName()))
                {
                    $databaseHandler->registerDatabaseConnexion($customer->getName());

                    // si on ajoute le select pour lier le customer à un utilisatuer dès sa création
                    // recupère le ou les utilisateur(s) selectionné(s)
                    $admins = $customer->getUsers()->getValues();
                    $permissionsHandler->createNewDatabaseAccessPermission($customer->getName(), $admins);
                }
                else
                    throw new \Exception(sprintf("Internal Error : Cannot create database '%s'", $customer->getName()));

            }

            if($request->request->get('contacts'))
            {

                $contacts = $request->request->get('contacts');

                if($this->allRequiredContactsDataExist($contacts))
                {
                    // if new contact was added, repo will added in $customer
                    $adminEm->getRepository(Customer::class)->addNewContact($contacts, $customer);
                }
                else
                {
                    $error = true;
                    $this->addFlash('error', 'Merci de renseigner tout les infos de vos contacts !');
                }

            }

            if(!$error)
            {
                $adminEm->persist($customer);
                $adminEm->flush();
            }

        }

        return $this->render('customer/create_customer.twig', [
            'form' => $form->createView(),
            'contacts' => $contacts ?? []
        ]);

    }


    /**
     * @Route(path="/edit/customer/{id}", name="customer::editCustomer", methods={"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function editCustomer(Request $request, Customer $customer)
    {

        $form = $this->createForm(CustomerCreationType::class, $customer);
        $form->handleRequest($request);

        $adminEm = $this->getDoctrine()->getManager('default');
        $error = false;

        if($form->isSubmitted() AND $form->isValid())
        {
            //dd($request->request);
            if($request->request->get('contacts'))
            {

                $contacts = $request->request->get('contacts');

                if($this->allRequiredContactsDataExist($contacts))
                {
                    $customerRepo = $adminEm->getRepository(Customer::class);

                    // if new contact was added, repo will added in $customer
                    $customerRepo->addNewContact($contacts, $customer);

                    // if contact was removed, repo will remove it from $customer
                    $customerRepo->removeUnnecessaryContact($contacts,$customer);
                }
                else
                {
                    $error = true;
                    $this->addFlash('error', 'Merci de renseigner tout les infos de vos contacts !');
                }

            }
            else
                $customer->removeAllContacts();

            if(!$error)
                $adminEm->flush();

        }

        return $this->render('customer/edit_customer.twig', [
            'form' => $form->createView(),
            'contacts' => $contacts ?? $customer->getContacts()->getValues(),
            'logoCustomer' => $customer->getLogo(),
        ]);

    }


    private function allRequiredContactsDataExist(array $contacts): bool
    {

        foreach ($contacts as $contact)
        {
            if(!array_key_exists('lastname', $contact) OR !array_key_exists('firstname', $contact) OR !array_key_exists('email', $contact)
                OR !array_key_exists('phonenumber', $contact) OR !array_key_exists('status', $contact))
                return false;


            elseif(empty($contact['lastname']) OR empty($contact['firstname']) OR empty($contact['email'])
                OR !filter_var($contact['email'], FILTER_VALIDATE_EMAIL) OR empty($contact['phonenumber'])
                    OR empty($contact['status']))
                return false;

        }

        return true;
    }
    

}