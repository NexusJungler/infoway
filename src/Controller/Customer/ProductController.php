<?php

namespace App\Controller\Customer;

use App\Entity\Customer\ExpectedChangesList;
use App\Entity\Customer\Product;
use App\Form\Customer\ProductType;
use App\Repository\Customer\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="customer_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('customer/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('customer_product_index');
        }

        return $this->render('customer/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('customer/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $expectedChangesList = new ExpectedChangesList();
        $formExpectedChanges = $this->createForm(ExpectedChangesList::class, $expectedChangesList, [
            'allowExpectedChanges' => false,
            'allowCurrentObjectChoice' => false
         ]);
        $formExpectedChanges->handleRequest($request);
        $form->handleRequest($request);

        if ($formExpectedChanges->isSubmitted() && $formExpectedChanges->isValid()) {
            dd('test');

            return $this->redirectToRoute('customer_product_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_product_index');
        }

        return $this->render('customer/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'formExpectedChanges' => $formExpectedChanges->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="customer_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_product_index');
    }
}
