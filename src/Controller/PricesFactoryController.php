<?php


namespace App\Controller;

use App\Entity\Admin\Entity;
use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Customer\PricesFactory;
use App\Entity\Customer\PriceType;
use App\Entity\Customer\Site;
use App\Entity\Customer\Product;
use App\Entity\Customer\MainPrice;
use App\Form\PricesFactoryType;
use App\Service\SessionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricesFactoryController extends AbstractController
{
    /**
     * @Route(path="pricesfactories/show", name="pricesfactories::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $factories = $em->getRepository(PricesFactory::class)->findAll();
        $sites = $em->getRepository(Site::class)->findAll();
        // create relation one to Many inversé dans site!
        foreach ($factories as $factory) {
            $factory->sites = [];
            foreach($sites as $site) {
                /*
                if ($site->getPricesFactory() == $factory) {
                    $factory->sites[] = $site;
                }
                */
                $factory->sites[] = $site;
            }
        }
        // dd($factories);

        return $this->render("pricesfactories/show.html.twig", [
            'factories' => $factories,
        ]);
    }

    /**
     * @Route(path="pricesfactory/create", name="pricesfactory::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request, SessionManager $sessionManager): Response
    {
        $factory = new PricesFactory();
        $factory->setCreatedAt(new \DateTime());
        $form = $this->createForm(PricesFactoryType::class, $factory);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $em->persist($factory);
            $em->flush();
            return $this->redirectToRoute('pricesfactories::show');
        }

        return $this->render("pricesfactories/create.html.twig", [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route(path="pricesfactory/edit/{factory}", name="pricesfactory::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PricesFactory $factory
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, PricesFactory $factory, SessionManager $sessionManager): Response
    {
        $form = $this->createForm(PricesFactoryType::class, $factory);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $em->flush();
            return $this->redirectToRoute('pricesfactories::show');
        }

        return $this->render("pricesfactories/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="pricesfactories/delete", name="pricesfactories::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, SessionManager $sessionManager): Response
    {
        $factories = $request->request->get('factories');
        if($factories != []) {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
            $rep = $em->getRepository( PricesFactoryType::class);
            foreach ($factories as $id => $val) {
                $factory = $rep->find($id);
                $em->remove($factory);
                $em->flush();
            }
        }
        return $this->redirectToRoute('pricesfactories::show');
    }

    /**
     * @Route(path="pricesfactories/ajax/getprices", name="pricesfactories::getprices", methods="POST")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function getPrices(Request $request, SessionManager $sessionManager): Response
    {
        $factories_ids = $request->request->get('factories');
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $entitiesManager = $this->getDoctrine()->getManager('default');

        $rep = $em->getRepository(PricesFactory::class);
        $rep_product = $em->getRepository(Product::class);
        $rep_price = $em->getRepository(MainPrice::class);
        $rep_time = $em->getRepository(Date::class);
        $rep_change = $em->getRepository(ExpectedChange::class);
        $rep_entities = $entitiesManager->getRepository(Entity::class);
        $rep_type =  $em->getRepository(PriceType::class); // No need if different product by priceType!

        $factories = $rep->findBy(['id' => $factories_ids]);
        $entity = $rep_entities->findOneBy(['name' => 'mainprice']);
        $dates = $rep_time->FindAll();
        // Filtre sur les dates pour ne conserver que celles qui concernent les prix
        foreach ($dates as $x => $date) {
            if($rep_change->findBy(['expectedAt' => $date, 'entity' => $entity->getId()]) === []) {
                unset($dates[$x]);
            }
        }
        $products = $rep_product->findAll();

        $products_prepare = [];
        foreach ($products as $product) {
            $prepared_tags = [];
            foreach($product->getTags() as $tag) {
                $prepared_tags[] = $tag->getName();
            }
            $products_prepare[$product->getId()] = ['name' => $product->getName(), 'category' => $product->getCategory()->getName(), 'amount' => $product->getAmount(), 'pricetype' => $product->getPriceType()->getName(), 'tags' => $prepared_tags];
        }

        $json = ['factories' => [], 'products' => $products_prepare, 'nbr_dates' => count($dates) + 1];

        foreach ($factories as $factory) {
            $prices = $rep_price->findBy(['factory' => $factory->getId()]);

            $prices_prepare = [];
            foreach ($prices as $price) {
                $product = $price->getProduct();
                $prices_prepare[$product->getId()] = ['price' => $price->getId(), 'day' => $price->getDayValue(), 'night' => $price->getNightValue()];
            }

            $datas_prepare = ['id' => $factory->getId(), 'name' => $factory->getName(), 'prices' => ['actuelle' => $prices_prepare]];
            foreach ($dates as $date) {
                $day = $date->getValue()->format('d-m-Y');
                $changes = $rep_change->findBy(['expectedAt' => $date->getId(), 'entity' => $entity->getId()]);
                $prices = [];

                // Traitement de la modification à date (récupération du produit concerné)
                foreach ($changes as $change) {
                    $datas = $change->getDatas();
                    $price = $rep_price->find($change->getInstance());
                    if($price->getFactory() == $factory) {
                        $prices[$price->getProduct()->getId()] = ['change' => $change->getId(), 'day' => $datas['day_value'], 'night' => ''];
                    }
                }
                $datas_prepare['prices'][$day] = $prices;
            }
            $json['factories'][] = $datas_prepare;
        }

        // dd($json);
        return new Response(json_encode($json));
    }

    /**
     * @Route(path="pricesfactories/ajax/saveprices", name="pricesfactories::saveprices", methods="POST")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function savePrices(Request $request, SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $entitiesManager = $this->getDoctrine()->getManager('default');
        $messages = [];

        $rep = $em->getRepository(PricesFactory::class);
        $rep_price = $em->getRepository(MainPrice::class);
        $rep_time = $em->getRepository(Date::class);
        $rep_change = $em->getRepository(ExpectedChange::class);
        $rep_entities = $entitiesManager->getRepository(Entity::class);
        $rep_product = $em->getRepository(Product::class);

        $factories = $request->request->get('factories');
        $entity = $rep_entities->findOneBy(['name' => 'mainprice']);
        // dd($factories);
        foreach ($factories as $factory_id => $dates) {
            $factory = $rep->find($factory_id);
            foreach ($dates as $date_string => $products) {
                foreach ($products as $product_id => $prices) {
                    $product = $rep_product->find($product_id);
                    // date présente
                    if($date_string == 'actuelle') {
                        if(isset($prices['price_id'])) {
                            // $price = $rep_price->findBy(['product' => $product, 'factory' => $factory]);
                            $price = $rep_price->find($prices['price_id']);
                            $price->setDayValue($prices['day']);
                        } else {
                            $price = new MainPrice();
                            $price->setFactory($factory);
                            $price->setProduct($product);
                            $price->setDayValue($prices['day']);
                            $em->persist($price);
                        }
                        $em->flush();

                    // date future
                    } else {
                        // Il faut impérativement avoir les 2 informations (les ids du prix et de la modification le cas échéant) en même temps dans ce cas de figure!
                        if(isset($prices['change_id'])) { // la modification a lieu sur une modification à date dejà existante dans le système
                            $change = $rep_change->find($prices['change_id']);
                            $json = $change->getDatas();
                            $json['day_value'] = $prices['day'];
                            $change->setDatas($json);
                            $change->setRequestedAt(new \DateTime());
                        } else {
                            $change = new ExpectedChange();
                            $change->setEntity($entity->getId());

                            // Vérification création nouvelle date ou pas
                            $expectedAt = $rep_time->findOneBy(['value' => new \DateTime($date_string)]);
                            //dd($expectedAt);
                            if ($expectedAt === null) {
                                $expectedAt = new Date();
                                $expectedAt->setValue(new \DateTime($date_string));
                                $em->persist($expectedAt);
                                $messages[] = 'La nouvelle date ' . $date_string . ' a bien été créée';
                            }

                            if(isset($prices['price_id'])) { // Normal Case => Javascript doit renvoyer l'id du prix contenu dans l'input de la date actuelle
                                $change->setInstance($prices['price_id']);
                            } else {
                                // Création du prix inexistant dont on demande la modification
                                /*
                                $price = new MainPrice();
                                $price->setFactory($factory);
                                $price->setProduct($product);
                                $price->setDayValue('0.00');
                                $em->persist($price);
                                $em->flush();
                                $change->setInstance($price->getId());
                                */

                                // Tentative de modification à date d'un prix inexistant
                                $messages[] = 'La modification à date d\'un prix non encore renseigné est impossible.';
                            }
                            $change->setExpectedAt($expectedAt);
                            $json = [
            //                    'factory' => $factory_id,
            //                    'product' => $product_id,
                                'day_value' => $prices['day']
                            ];
                            $change->setDatas($json);
                            $change->setRequestedAt(new \DateTime());
                            $em->persist($change);
                        }
                        $em->flush();
                    }
                }
            }
        }
        return new Response(json_encode($messages));
    }
}