<?php

namespace App\Controller\Programming;

use App\Entity\Customer\DisplayMould;
use App\Form\Customer\DisplayMouldType;
use App\Repository\Customer\DisplayMouldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programming/display-mould")
 */
class DisplayMouldController extends AbstractController
{
    /**
     * @Route("/", name="programming_display_mould_index", methods={"GET"})
     */
    public function index(DisplayMouldRepository $displayMould): Response
    {
        return $this->render('programming/display_mould/index.html.twig', [
            'display_moulds' => $displayMould->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="programming_display_mould_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $displayMould = new DisplayMould();
        $form = $this->createForm(DisplayMouldType::class, $displayMould);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($displayMould);
            $entityManager->flush();

            return $this->redirectToRoute('programming_display_mould_index');
        }

        return $this->render('programming/display_mould/new.html.twig', [
            'display_mould' => $displayMould,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_mould_show", methods={"GET"})
     */
    public function show(DisplayMould $displayMould): Response
    {
        return $this->render('programming/display_mould/show.html.twig', [
            'display_mould' => $displayMould,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programming_display_mould_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DisplayMould $displayMould): Response
    {
        $form = $this->createForm(DisplayMouldType::class, $displayMould);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programming_display_mould_index');
        }

        return $this->render('programming/display_mould/edit.html.twig', [
            'display_mould' => $displayMould,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_mould_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DisplayMould $displayMould): Response
    {
        if ($this->isCsrfTokenValid('delete'.$displayMould->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($displayMould);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programming_display_mould_index');
    }
}
