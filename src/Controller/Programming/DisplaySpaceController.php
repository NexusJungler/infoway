<?php

namespace App\Controller\Programming;

use App\Entity\Admin\Customer;
use App\Entity\Customer\DisplayMould;
use App\Entity\Customer\DisplaySpace;
use App\Entity\Customer\TimeSlot;
use App\Form\Customer\DisplayMouldType;
use App\Form\Customer\DisplaySpaceType;
use App\Repository\Customer\DisplaySpaceRepository;
use PhpParser\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;

/**
 * @Route("/programming/display-space")
 */
class DisplaySpaceController extends AbstractController
{
    /**
     * @Route("/", name="programming_display_space_index", methods={"GET"})
     */
    public function index(DisplaySpaceRepository $displaySpaceRepository): Response
    {
        return $this->render('programming/display_space/index.html.twig', [
            'display_spaces' => $displaySpaceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="programming_display_space_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session): Response
    {

        $currentCustomer = $session->get('current_customer') ;
        if( ! $currentCustomer instanceof Customer ) throw new Error('invalid customer ') ;

        $displaySpace = new DisplaySpace();
        $form = $this->createForm(DisplaySpaceType::class, $displaySpace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager( $currentCustomer->getName() );
            $entityManager->persist($displaySpace);
            $entityManager->flush();

            return $this->redirectToRoute('programming_display_space_index');
        }

        return $this->render('programming/display_space/new.html.twig', [
            'display_space' => $displaySpace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_space_show", methods={"GET", "POST"})
     */
    public function show(DisplaySpace $displaySpace, Request $request): Response
    {

        $displayMould = new DisplayMould() ;
        $displayMould->setDisplaySpace( $displaySpace ) ;

        $defaultTimeSlot = new TimeSlot() ;
        $defaultTimeSlot->setStartAt(new \DateTime('00:00'));
        $defaultTimeSlot->setEndAt(new \DateTime('00:00'));

        $displayMould->addTimeSlot( $defaultTimeSlot ) ;

        $optionsToPassToForm = [
            'allowPlaylistCreation' => false,
            'allowDisplaySpaceChoice' => false,
            'allowModelChoice'   => true
            ];

        $form = $this->createForm(DisplayMouldType::class, $displayMould, $optionsToPassToForm );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($displayMould);

            return $this->redirectToRoute('programming_display_space_index');
        }
        return $this->render('programming/display_space/show.html.twig', [
            'display_space' => $displaySpace,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programming_display_space_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DisplaySpace $displaySpace): Response
    {
        $form = $this->createForm(DisplaySpaceType::class, $displaySpace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programming_display_space_index');
        }

        return $this->render('programming/display_space/edit.html.twig', [
            'display_space' => $displaySpace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_space_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DisplaySpace $displaySpace): Response
    {
        if ($this->isCsrfTokenValid('delete'.$displaySpace->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($displaySpace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programming_display_space_index');
    }
}
