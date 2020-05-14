<?php


namespace App\Controller;

use App\Entity\Admin\Entity;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Customer\PriceType;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Admin\Allergen;
use App\Entity\Customer\Product;
use App\Entity\Customer\Category;
use App\Entity\Customer\ProductAllergen;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function getProductsList(int $category_id): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
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
    public function show(): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
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
    public function create(Request $request): Response
    {

        $product = new Product();
        $product->setCreatedAt(new \DateTime());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
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
    public function edit(Request $request, Product $product): Response
    {
        $expected_change_service = new ExpectedChangeService($this->getDoctrine());

        $product->setAllergens(new ArrayCollection());
        $form = $this->createForm(ProductType::class, $product);

        $expected_change_forms = [];

        /*
        $expected_change_forms = $this->forward('App\Controller\ExpectedChangeController::getForms', [
            'classname'  => 'product',
            'id' => $product->getId(),
        ]);
        */
        // dd($expected_change_forms);

        $rep_change = $this->getDoctrine()->getRepository(ExpectedChange::class, 'kfc');
        $entity = $this->getDoctrine()->getRepository(Entity::class, 'default')->findOneBy(['name' => 'product']);
        $all_change = $rep_change->findBy(['entity' => $entity->getId(), 'instance' => $product->getId()]);

        foreach($all_change as $change) {
            // dd($change);
            $datas = $change->getDatas();
            $product->setName($datas['name']);
            $product->setDescription($datas['description']);
            $product->setNote($datas['note']);
            $product->setAmount($datas['amount']);
            $category = $this->getDoctrine()->getRepository(Category::class, 'kfc')->find($datas['category']);
            $product->setCategory($category);
            $type = $this->getDoctrine()->getRepository(PriceType::class, 'kfc')->find($datas['priceType']);
            $product->setPricetype($type);

            $start = new \Datetime();
            $start->setTimestamp($datas['start']);
            $product->setStart($start);
            $end = new \Datetime();
            $end->setTimestamp($datas['end']);
            $product->setEnd($end);

            $page= $this->get('form.factory')->createNamed('toto', ProductType::class, $product);
            // dd($page);
            $expectedDate = $change->getExpectedAt()->getValue()->format('Y-m-d');
            $expected_change_forms[$expectedDate] = $page->createView();
        }

        // dd($expected_change_forms);
        $form->handleRequest($request);

        $rep_allergen = $this->getDoctrine()->getRepository(Allergen::class, 'default');
        $rep_allergen_relation = $this->getDoctrine()->getRepository(ProductAllergen::class, 'kfc');
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
            $em = $this->getDoctrine()->getManager('kfc');

            if($request->request->get('expected_date') !== null ){
                $product_id = $product->getId();
                // $product = null;
                $em->detach($product);

                $expected_change_service->update($request, 'product', $product_id);
                // $em->flush();
                // $service->update($request, 'product', $product->getId());

                /*
                $this->forward('App\Controller\ExpectedChangeController::update', [
                    'request'  => $request,
                    'classname' => 'product',
                    'id' => $product->getId()
                ]);
                */

                return $this->redirectToRoute('product::edit', ['product' => $product_id]);
            } else {
                $updated_product_allergen_ids = $request->request->get('allergens');

                if($updated_product_allergen_ids !== null) {
                    foreach ($updated_product_allergen_ids as $updated_allergen_id) {
                        if(!in_array($updated_allergen_id, $current_product_allergen_ids)) {
                            $new_association = new ProductAllergen();
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
                }

                $em->flush();
                return $this->redirectToRoute('products::show');
            }

        }

        return $this->render("products/create.html.twig", [
            'form' => $form->createView(),
            'allergens' => $allergens,
            'product_allergens'=> $current_product_allergens,
            'expected_change_forms' => $expected_change_forms
        ]);
    }

    /**
     * @Route(path="products/delete", name="products::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $products = $request->request->get('products');
        if($products != []) {
            $em = $this->getDoctrine()->getManager('kfc');
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
    public function duplicate(Request $request, Product $product): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $name = $product->getName();
        $wished_properties = array_keys($request->request->get('product'));

        $clone = new Product();
        $clone->setName($name . '_copy');
        $clone->setCreatedAt(new \DateTime());

        foreach ($wished_properties as $property) {
            $methodGET = 'get' . ucfirst($property);
            $methodSET = 'set' . ucfirst($property);
            if(method_exists(Product::class, $methodGET) && method_exists(Product::class, $methodSET)) {
                if($product->$methodGET() !== null) {
                    $clone->$methodSET($product->$methodGET());
                }
            }
        }

        $em->persist($clone);
        $em->flush();
        return $this->redirectToRoute('product::edit',['product' => $clone->getId()]);
    }

    /**
     * @Route(path="product/test/{product_id}", name="product::test", methods={"GET","POST"})
     *
     * @param Request $request
     * @param int $product_id
     * @return Response
     * @throws \Exception
     */
    public function test(Request $request, int $product_id): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
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
            $em = $this->getDoctrine()->getManager('kfc');
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