<?php

namespace App\Controller\Customer;

use App\Entity\Customer\ExpectedChange;
use App\Entity\Customer\Product;
use App\Form\CategoryType;
use App\Form\Customer\ExpectedChangeType;
use App\Repository\Customer\ExpectedChangeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer/expected/change")
 */
class ExpectedChangeController extends AbstractController
{
    /**
     * @Route("/", name="customer_expected_change_index", methods={"GET"})
     */
    public function index(ExpectedChangeRepository $expectedChangeRepository): Response
    {
        return $this->render('customer/expected_change/index.html.twig', [
            'expected_changes' => $expectedChangeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_expected_change_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $expectedChange = new ExpectedChange();
        $em = $this->getDoctrine()->getManager('kfc') ;

        $repo = $em->getRepository(Product::class);
        $product = $repo->findOneBy(['id' => 4 ]) ;

        // $expectedChange->setEntityObject($product);

        $form = $this->createForm(ExpectedChangeType::class, $expectedChange, [
            'entityToChange' => new CategoryType()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($expectedChange);
            $em->flush();

            return $this->redirectToRoute('customer_expected_change_index');
        }

        return $this->render('customer/expected_change/new.html.twig', [
            'expected_change' => $expectedChange,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_expected_change_show", methods={"GET"})
     */
    public function show(ExpectedChange $expectedChange): Response
    {
        return $this->render('customer/expected_change/show.html.twig', [
            'expected_change' => $expectedChange,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_expected_change_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ExpectedChange $expectedChange): Response
    {
        $form = $this->createForm(ExpectedChange1Type::class, $expectedChange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_expected_change_index');
        }

        return $this->render('customer/expected_change/edit.html.twig', [
            'expected_change' => $expectedChange,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_expected_change_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ExpectedChange $expectedChange): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expectedChange->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($expectedChange);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_expected_change_index');
    }
}
