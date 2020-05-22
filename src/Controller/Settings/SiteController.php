<?php

namespace App\Controller\Settings;

use App\Entity\Admin\Customer;
use App\Entity\Admin\Screen;
use App\Entity\Admin\ScreensList;
use App\Entity\Customer\Site;
use App\Entity\Customer\SiteScreen;
use App\Form\Admin\ScreensListType;
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
        $siteScreen = new SiteScreen() ;
        $siteScreen->setScreen( $this->getDoctrine()->getRepository(Screen::class)->findOneBy(['id' => 1]))  ;
        $site->addSiteScreen( $siteScreen  ) ;
//        $this->getDoctrine()->getManager('kfc')->persist($site);
//        $siteScreen = new SiteScreen() ;
//
//        $siteScreen->setScreenId(1);
//        $site->addSiteScreen($siteScreen);

        $currentCustomer = $session->get('current_customer') ;
        $creator = $session->get('user') ;

        $datasToPassToSiteForm = [
            'customer' => $currentCustomer,
            'creator' => $creator
        ] ;
        $datasToPassToScreensListForm = [
            'showOnlyAvailablesScreens' => true
        ] ;

        $screensList = new ScreensList() ;
        $screenListForm = $this->createForm(ScreensListType::class, $screensList , $datasToPassToScreensListForm );
        $form = $this->createForm(SiteType::class, $site, $datasToPassToSiteForm ) ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            dd($site) ;



            $site->setCustomer( $currentCustomer );

            $entityManager = $this->getDoctrine()->getManager($currentCustomer->getName());

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('sites_index');
        }

        return $this->render('sites/new.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
            'screensListForm' => $screenListForm->createView(),
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
    public function edit(Request $request, Site $site, SessionInterface $session): Response
    {

        $currentCustomer = $session->get('current_customer') ;
        $creator = $session->get('user') ;

        $datasToPassToSiteForm = [
            'customer' => $currentCustomer,
            'creator' => $creator
        ] ;

        $screensIdToImport = [] ;

        foreach( $site->getSiteScreens() as $siteScreen ) {  $screensIdToImport[] = $siteScreen->getScreenId() ; }

        $importatedScreensFromDb = $this->getDoctrine()->getRepository(Screen::class)->findBy(['id' => $screensIdToImport]) ;

        $importatedScreensFromDbSortedById = [] ;

        foreach( $importatedScreensFromDb as $importatedScreen){  $importatedScreensFromDbSortedById[ $importatedScreen->getId() ] = $importatedScreen ;  }

        foreach( $site->getSiteScreens() as $siteScreen ){
            $screenFromDb = $importatedScreensFromDbSortedById[ $siteScreen->getScreenId()] ?? null ;
            if( $screenFromDb !== null  ){ $siteScreen->setScreen( $screenFromDb ) ; }
            else{ $site->removeSiteScreen( $siteScreen ) ; }
        }

        $form = $this->createForm(SiteType::class, $site, $datasToPassToSiteForm);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em =  $this->getDoctrine()->getManager('kfc') ;


            foreach( $site->getSiteScreens() as $siteScreen ){

                if($siteScreen->getLocalProgramming() !== null ){
                    foreach( $siteScreen->getLocalProgramming()->getEntitiesToRemoveFromDb() as $entityToRemoveFromDb ){
                        $em->remove( $entityToRemoveFromDb );
                    };
                }
            }

            $this->getDoctrine()->getManager('kfc')->flush();

            return $this->redirectToRoute('sites_index');
        }

        return $this->render('sites/edit.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
            'currentCustomer' => $currentCustomer
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
