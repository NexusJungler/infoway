<?php

namespace App\Controller;

use App\Entity\Customer\Criterion;
use App\Form\Customer\CriterionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CriterionController extends AbstractController
{
    /**
     * @Route("/criterion/create", name="criterion")
     */
    public function index()
    {
        return $this->render('criterion/index.html.twig', [
            'controller_name' => 'CriterionController',
        ]);
    }

    /**
     * @Route("/criterion/{id}/edit", name="criterion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Criterion $criterion): Response
    {
        $form = $this->createForm(CriterionType::class, $criterion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('criterion_list_index');
        }

        return $this->render('criterion_list/edit.html.twig', [
            'criterion_list' => $criterion,
            'form' => $form->createView(),
        ]);
    }
}
