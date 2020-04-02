<?php


namespace App\Controller;


use App\Form\CustomerCreationType;
use App\Service\{DatabaseAccessHandler, PermissionsHandler};
use App\Entity\Admin\{ Customer };
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    /**
     * @Route(path="/create/customer", name="admin::createCustomer", methods={"GET", "POST"})
     * @param Request $request
     * @param DatabaseAccessHandler $databaseHandler
     * @param PermissionsHandler $permissionsHandler
     * @return Response
     */
    public function createCustomer(Request $request, DatabaseAccessHandler $databaseHandler, PermissionsHandler $permissionsHandler): Response
    {

        $customer = new Customer();
        $form = $this->createForm(CustomerCreationType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid())
        {

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

            $this->getDoctrine()->getManager('default')->persist($customer);
            $this->getDoctrine()->getManager('default')->flush();

        }

        return $this->render('admin/admin.createCustomer.twig', [
            'form' => $form->createView()
        ]);

    }

}