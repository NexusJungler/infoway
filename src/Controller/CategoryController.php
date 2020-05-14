<?php


namespace App\Controller;

use App\Entity\Customer\Category;
use App\Entity\Customer\Product;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route(path="categories/show", name="categories::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render("categories/show.html.twig", [
            'categories' => $categories
        ]);
    }

    /**
     * @Route(path="category/create", name="category::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $category = new Category();
        $category->setCreatedAt(new \DateTime());

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('categories::show');
        }

        return $this->render("categories/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="category/edit/{category}", name="category::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, Category $category): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $products = $em->getRepository(Product::class)->findBy(['category' => $category]);
        foreach ($products as $product) {
            $category->addProduct($product);
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        //dd($category);

        if($form->isSubmitted() && $form->isValid())
        {
            // dd($category->getProducts());
            $em = $this->getDoctrine()->getManager('kfc');
            $em->flush();
            return $this->redirectToRoute('categories::show');
        }

        return $this->render("categories/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="categories/delete", name="categories::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $categories = $request->request->get('categories');
        if($categories != []) {
            $em = $this->getDoctrine()->getManager('kfc');
            $rep = $em->getRepository( Category::class);
            foreach ($categories as $id => $val) {
                $category = $rep->find($id);
                $em->remove($category);
                $em->flush();
            }
        }
        return $this->redirectToRoute('categories::show');
    }

}