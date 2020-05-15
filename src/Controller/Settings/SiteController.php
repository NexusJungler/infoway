<?php

namespace App\Controller\Settings;

use App\Entity\Admin\Customer;
use App\Entity\Customer\Site;
use App\Form\SiteType;
use App\Repository\Customer\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sites")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/", name="sites_index", methods={"GET"})
     */
    public function index(SiteRepository $siteRepository): Response
    {
        return $this->render('sites/index.html.twig', [
            'sites' => $siteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sites_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session): Response
    {
        $site = new Site();

        $currentCustomer = $session->get('current_customer') ;
        $creator = $session->get('user') ;

        $datasToPassToSiteForm = [
            'customer' => $currentCustomer,
            'creator' => $creator
        ] ;

        $form = $this->createForm(SiteType::class, $site, $datasToPassToSiteForm ) ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $site->setCustomer( $currentCustomer );

            $entityManager = $this->getDoctrine()->getManager($currentCustomer->getName());

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('sites_index');
        }

        return $this->render('sites/new.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
            'currentCustomer' => $currentCustomer
        ]);
    }

    /**
     * @Route("/{id}", name="sites_show", methods={"GET"})
     */
    public function show(Site $site): Response
    {
        return $this->render('sites/show.html.twig', [
            'site' => $site,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sites_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Site $site): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sites_index');
        }

        return $this->render('sites/edit.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sites_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Site $site): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($site);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sites_index');
    }
}
