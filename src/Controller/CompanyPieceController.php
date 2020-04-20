<?php


namespace App\Controller;


use App\Entity\Customer\CompanyPiece;
use App\Form\CreateCompanyPieceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyPieceController extends AbstractController
{

    /**
     * @Route(path="/show/all/sites", name="admin_site::showAll", methods="GET")
     * @return Response
     */
    public function showAll(): Response
    {

        $form = $this->createForm( CreateCompanyPieceType::class, new CompanyPiece());

        $customer = [
            'ARES',
            'Q087',
            'AEAS',
            'Q087A2',
            'ARAS',
            'Q08'
        ];

        return $this->render("setting.html.twig", [
            'form' => $form->createView(),
            'permissionToCreate' => 'toto',
            'customer' => $customer
        ]);

    }

    /**
     * @Route(path="/create/site", name="admin_site::create", methods="POST|GET")
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {

        die();

        /*$site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->persist($site);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("message", "Site crée !");

            return $this->redirectToRoute("admin_site::show", [
                'id' => $site->getId(),
                'name' => $site->getName()
            ]);

        }

        return $this->render("back-office/site/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Ajouter une entreprise",
            'action' => 'Créer'
        ]);*/

    }


    /**
     * @Route(path="/show/site/{id}-{name}", name="admin_site::show", methods="GET")
     *
     * @return Response
     */
    public function show(): Response
    {

        die();

        /*$form = $this->createForm(SiteType::class, $site);

        return $this->render("back-office/site/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Voir un site",
            'site' => $site
        ]);*/

    }


    /**
     * @Route(path="/edit/site/{id}-{name}", name="admin_site::edit", methods="POST|GET")
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {

        die();

        /*$form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("message", "Site modifié !");

            return $this->redirectToRoute("admin_site::show", [
                'id' => $site->getId(),
                'name' => $site->getName()
            ]);

        }

        return $this->render("back-office/site/create_show_edit.html.twig", [
            'form' => $form->createView(),
            'title' => "Editer un site",
            'action' => 'Editer'
        ]);*/

    }


    /**
     * @Route(path="/delete/site/{id}-{name}", name="admin_site::delete", methods="DELETE")
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
/*        if($this->isCsrfTokenValid("delete". $site->getId(), $request->get("_token")))
        {

            $this->getDoctrine()->getManager()->remove($site);

            $this->getDoctrine()->getManager()->flush();
        }
        else
        {
            $this->addFlash("error", "Token CSRF non valide !");
        }

        return $this->redirectToRoute('admin_customer::showAll');*/
        die();
    }

}