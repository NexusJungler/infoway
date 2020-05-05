<?php


namespace App\Controller;

use App\Entity\Admin\Allergen;
use App\Entity\Customer\Product;
use App\Entity\Customer\Category;
use App\Entity\Customer\ProductAllergens;
use App\Service\SessionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ProductType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController
{
    /**
     * @var Serializer
     */
    private $__serializer;

    public function __construct()
    {
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__', 'date']
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $this->__serializer = new Serializer([$normalizer], [$encoder]);
    }

    /**
     * @Route(path="ajax/products/list/{category_id}/", name="products::list", methods="GET",
     *     requirements={"category_id": "\d+"})
     *
     * @param int $category_id
     * @return Response
     * @throws \Exception
     */
    public function getProductsList(int $category_id, SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $rep = $em->getRepository(Product::class);
        $products = $rep->findBy(['category' => $category_id]);
        $json = $this->__serializer->serialize($products,'json');

        $test = json_decode($json, true);
        foreach($test as $i => $product) {
            unset($product['createdAt']);
            unset($product['start']);
            unset($product['end']);
            $test[$i] = $product;
        }
        //dd($json);
        // (json_decode($json, true));
        return new Response(json_encode($test));
    }

    /**
     * @Route(path="products/show", name="products::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $products = $em->getRepository( Product::class)->findAll();
        $categories = $em->getRepository(Category::class)->findAll();
        //dd($products);

        return $this->render("products/show.html.twig", [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @Route(path="product/create", name="product::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request, SessionManager $sessionManager): Response
    {

        $product = new Product();
        $product->setCreatedAt(new \DateTime());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('products::show');
        }

        return $this->render("products/create.html.twig", [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route(path="product/edit/{product}", name="product::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, Product $product, SessionManager $sessionManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $rep_allergen = $this->getDoctrine()->getRepository(Allergen::class, 'default');
        $rep_allergen_relation = $this->getDoctrine()->getRepository(ProductAllergens::class, strtolower( $sessionManager->get('userCurrentCustomer') ));
        $allergens = $rep_allergen->findAll();

        $current_product_allergens_relation = $rep_allergen_relation->findBy(['product' => $product]);
        $current_product_allergen_ids = [];
        $current_product_allergens = [];

        foreach ($current_product_allergens_relation as $allergen_relation) {
            $id = $allergen_relation->getAllergenId();
            $current_product_allergens[] = $rep_allergen->find($id);
            $current_product_allergen_ids[] = $id;
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $updated_product_allergen_ids = $request->request->get('allergens');
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));

            foreach ($updated_product_allergen_ids as $updated_allergen_id) {
                if(!in_array($updated_allergen_id, $current_product_allergen_ids)) {
                    $new_association = new ProductAllergens();
                    $new_association->setProduct($product);
                    $new_association->setAllergenId($updated_allergen_id);
                    $em->persist($new_association);
                }
            }

            foreach ($current_product_allergen_ids as $id) {
                if(!in_array($id, $updated_product_allergen_ids)) {
                    $current_association = $rep_allergen_relation->findOneBy(['product' => $product, 'allergenId' => $id]);
                    $em->remove($current_association);
                }
            }

            $em->flush();
            return $this->redirectToRoute('products::show');
        }

        return $this->render("products/create.html.twig", [
            'form' => $form->createView(),
            'allergens' => $allergens,
            'product_allergens'=> $current_product_allergens
        ]);
    }

    /**
     * @Route(path="products/delete", name="products::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, SessionManager $sessionManager): Response
    {
        $products = $request->request->get('products');
        if($products != []) {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $rep = $em->getRepository( Product::class);
            foreach ($products as $id => $val) {
                $product = $rep->find($id);
                $em->remove($product);
                $em->flush();
            }
        }
        return $this->redirectToRoute('products::show');
    }

    /**
     * @Route(path="product/duplicate/{product}", name="product::duplicate", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws \Exception
     */
    public function duplicate(Request $request, Product $product, SessionManager $sessionManager): Response
    {
        // $clone = clone($product);
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $name = $product->getName();

        $clone = new Product();
        $clone->setName($name . '_copy');
        $clone->setCreatedAt(new \DateTime());
        $clone->setCategory($product->getCategory());
        $clone->setPriceType($product->getPriceType());
        $clone->setAmount($product->getAmount());
        $description = $product->getDescription();
        if ($description == null) {
            $description = '';
        }
        $mention = $product->getNote();
        if ($mention == null) {
            $mention = '';
        }
        $clone->setDescription($description);
        $clone->setStart($product->getStart());
        $clone->setEnd($product->getEnd());
        $clone->setNote($mention);
        $clone->setLogo($product->getLogo());

        $em->persist($clone);
        $em->flush();
        return $this->redirectToRoute('products::show');
    }

    /**
     * @Route(path="product/test/{product_id}", name="product::test", methods={"GET","POST"})
     *
     * @param Request $request
     * @param int $product_id
     * @return Response
     * @throws \Exception
     */
    public function test(Request $request, int $product_id, SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $rep = $em->getRepository(Product::class);
        $product = $rep->find($product_id);
        dd($product->getTags());
        $clone = clone($product);
        $form = $this->createForm(ProductType::class, $clone);
        $form->handleRequest($request);
        $name = $clone->getName();

        if($form->isSubmitted() && $form->isValid())
        {
            dd($product->getTags());
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $clone->setName($name . '_copy');
            $em->persist($clone);
            $em->flush();
            return $this->redirectToRoute('products::show');
        }

        return $this->render("products/create.html.twig", [
            'form' => $form->createView()
        ]);

    }



}