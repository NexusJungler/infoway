<?php


namespace App\Controller;

use App\Entity\Admin\Entity;
use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Customer\ExpectedChangesList;
use App\Entity\Customer\PriceType;
use App\Form\Customer\DateType;
use App\Form\Customer\ExpectedChangesListType;
use App\Serializer\Normalizer\IgnoreNotAllowedNulledAttributeNormalizer;
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
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends AbstractController
{
    /**
     * @var Serializer
     */
    private $_serializer;

    private $_session;

    public function __construct()
    {
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            // AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__', 'date']
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        // $normalizer = new IgnoreNotAllowedNulledAttributeNormalizer(null, null, null, null, null, null, $defaultContext);
        $this->_serializer = new Serializer([$normalizer], [$encoder]);

        $this->_session = new Session();
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
        $json = $this->_serializer->serialize($products,'json');

        $clean = json_decode($json, true);
        //dd($clean);
        foreach($clean as $i => $product) {
            if($product['start'] !== null) {
                $start = new \DateTime();
                $start->setTimestamp($product['start']['timestamp']);
                $product['start'] = $start->format('d-m-Y');
            }

            if($product['end'] !== null) {
                $end = new \DateTime();
                $end->setTimestamp($product['end']['timestamp']);
                $product['end'] = $end->format('d-m-Y');
            }
            
            unset($product['createdAt']);
            $clean[$i] = $product;
        }
        //dd($json);
        // (json_decode($json, true));
        return new Response(json_encode($clean));
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
        $rep_allergen = $this->getDoctrine()->getRepository(Allergen::class, 'default');
        $rep_allergen_relation = $em->getRepository(ProductAllergen::class);

        foreach ($products as $product) {
            $current_product_allergens_relation = $rep_allergen_relation->findBy(['product' => $product]);
            foreach ($current_product_allergens_relation as $allergen_relation) {
                $id = $allergen_relation->getAllergenId();
                $product->addAllergen($rep_allergen->find($id));
            }
        }
        // dd($products);

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
            $mediaFile = $form->get('logo')->getData();
            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                try {
                    $mediaFile->move(
                        $this->getParameter('logoDirectory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $product->setLogo($newFilename);
            }

            $em = $this->getDoctrine()->getManager('kfc');
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('products::show');
        }

        $rep_allergen = $this->getDoctrine()->getRepository(Allergen::class, 'default');
        $allergens = $rep_allergen->findAll();

        return $this->render("products/create.html.twig", [
            'form' => $form->createView(),
            'allergens' => $allergens,
            'product_allergens'=> [],
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
        // $expected_change_service = new ExpectedChangeService($this->getDoctrine());

        $expectedChangesList = new ExpectedChangesList();
        $expectedChangesList->setCurrentObject($product);
        $newDate = new Date();
        $newDate->setValue(new \DateTime());
        $formExpectedChanges = $this->createForm(ExpectedChangesListType::class, $expectedChangesList, [
            'allowExpectedChanges' => false,
            'allowCurrentObjectChoice' => false
        ]);

        $formExpectedChanges->handleRequest($request);

        $formCreateDate = $this->createForm(DateType::class, $newDate);
        $formCreateDate->handleRequest($request);

        $product->setAllergens(new ArrayCollection());
        $form = $this->createForm(ProductType::class, $product);

        /*
        $expected_change_forms = [];
        $expected_change_forms = $this->forward('App\Controller\ExpectedChangeController::getForms', [
            'classname'  => 'product',
            'id' => $product->getId(),
        ]);
        */

        $rep_change = $this->getDoctrine()->getRepository(ExpectedChange::class, 'kfc');
        $entity = $this->getDoctrine()->getRepository(Entity::class, 'default')->findOneBy(['name' => 'product']);
        $all_change = $rep_change->findBy(['entity' => $entity->getId(), 'instance' => $product->getId()]);

        /*
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

            // $page = $this->get('form.factory')->createNamed('toto', ProductType::class, $product);
            $page = $this->createForm(ExpectedChangeType::class, $expectedChange, [
                'entityToChange' => new CategoryType()
            ]);

            $expectedDate = $change->getExpectedAt()->getValue()->format('Y-m-d');
            $expected_change_forms[$expectedDate] = $page->createView();
        }
        */

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

        if ($formExpectedChanges->isSubmitted() && $formExpectedChanges->isValid()) {

            dump($expectedChangesList);
            $serializedObject = $this->_serializer->serialize($expectedChangesList, 'json');
            dump($serializedObject);
            // $deserializedObject = $this->_serializer->deserialize($serializedObject, ExpectedChangesList::class, 'json');
            $deserializedObject= json_decode($serializedObject);
            $this->_session->set('serializedObject', $serializedObject);

            return $this->redirectToRoute('expected_change::savebis', ['classname' => 'product', 'id' => $product->getId()]);
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');

                $updated_product_allergen_ids = $request->request->get('allergens');
                dump($updated_product_allergen_ids);

                if($updated_product_allergen_ids !== null) {
                    foreach ($updated_product_allergen_ids as $updated_allergen_id) {
                        if(!in_array($updated_allergen_id, $current_product_allergen_ids)) {
                            $new_association = new ProductAllergen();
                            $new_association->setProduct($product);
                            $new_association->setAllergenId($updated_allergen_id);
                            $em->persist($new_association);
                        }
                    }

                    dump($current_product_allergen_ids);
                    foreach ($current_product_allergen_ids as $id) {
                        if(!in_array($id, $updated_product_allergen_ids)) {
                            $current_association = $rep_allergen_relation->findOneBy(['product' => $product, 'allergenId' => $id]);
                            dump($current_association);
                            $em->remove($current_association);
                        }
                    }
                }

            $mediaFile = $form->get('logo')->getData();
            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                try {
                    $mediaFile->move(
                        $this->getParameter('logoDirectory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $product->setLogo($newFilename);
            }
                // dd('exit');
                $em->flush();
                return $this->redirectToRoute('products::show');

        }

        $dates = $this->getDoctrine()->getRepository(Date::class, 'kfc')->findAll();

        return $this->render("products/create.html.twig", [
            'form' => $form->createView(),
            'allergens' => $allergens,
            'product_allergens'=> $current_product_allergens,
            'dates' => $dates,
            'formExpectedChanges' => $formExpectedChanges->createView(),
            'formCreateDate' => $formCreateDate->createView()
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

    /**
     * @Route(path="/create-date", name="app:date")
     *
     * @param Request $request
     * @return Response
     */
    public function date(Request $request): Response
    {

        return $this->render("pricesfactories/manage.html.twig", [

        ]);

    }

}