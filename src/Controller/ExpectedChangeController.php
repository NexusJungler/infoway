<?php


namespace App\Controller;

use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Admin\Entity;
use App\Entity\Customer\ExpectedChangesList;
use App\Entity\Customer\Product;
use App\Form\CategoryType;
use App\Form\Customer\ExpectedChangesListType;
use App\Form\Customer\ExpectedChangeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

class ExpectedChangeController extends AbstractController
{
    private $_session;
    private $__serializer;
    private $__memory;
    private $_selectedDates;

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

        $this->_session = new Session();
        // $this->_session->start();
    }

    /**
     * @Route(path="expected_change/{classname}/{id}", name="expected_change::save", methods={"GET","POST"})
     *
     * @param Request $request
     * @param string $classname
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function save(Request $request, string $classname, int $id): Response
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        if($entity === null) {
            dd('Erreur: Aucune entité ' . $classname . ' n\'existe dans le system!');
        }

        $em = $this->getDoctrine()->getManager('kfc');
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
        $entityForm = "App\Form\\" . ucfirst($classname) . 'Type';
        $instance = $em->getRepository($entityPath)->find($id);
        $this->__memory = json_decode($this->__serializer->serialize($instance, 'json'),true);
        //dd($instance);

        $form = $this->createForm($entityForm, $instance);
        $form->handleRequest($request);

        //dump(json_decode($this->__memory, true));

        if($form->isSubmitted() && $form->isValid())
        {
            // dd($request);
            // dd($instance);
            $date_string = $request->request->get('expected_date');
            $change_id = $request->request->get('expected_change');
            $rep_time = $em->getRepository(Date::class);
            $rep_change = $em->getRepository(ExpectedChange::class);
            $expectedAt = $rep_time->findOneBy(['value' => new \DateTime($date_string)]);

            if ($expectedAt === null) {
                $expectedAt = new Date();
                $expectedAt->setValue(new \DateTime($date_string));
                $em->persist($expectedAt);
            }
            if ($change_id !== '') {
                // update changement
                $change = $rep_change->find($change_id);
            } else {
                // création changement
                $change = new ExpectedChange();
                $change->setExpectedAt($expectedAt);
                $change->setEntity($entity->getId());
            }

            $json = $this->__serializer->serialize($instance, 'json');
            $cleaned_datas = json_decode($json, true);

            $class_name = get_class($instance);
            $methods = get_class_methods($class_name);

            foreach ($methods as $method) {
                $property = lcfirst(substr($method, 3));
                if(substr($method, 0, 3) === 'get') {
                    $reflection = new \ReflectionMethod($entityPath, $method);
                    $field_type = $reflection->getReturnType()->getName();

                    if($field_type === 'DateTimeInterface') {
                        $date = $instance->$method();
                        $cleaned_datas[$property] = $date->getTimestamp();
                    }

                    if(strpos($field_type, 'App\Entity\Customer\\') === 0 ) {
                        $cleaned_datas[$property] = $instance->$method()->getId();
                    }

                    if($field_type === 'Doctrine\Common\Collections\Collection') {
                        $array = [];
                        foreach ($instance->$method() as $item) {
                            $array[] = $item->getId();
                        }
                        $cleaned_datas[$property] = $array;
                    }
                }
            }

            // dd($this->__memory, $cleaned_datas);
            $change->setDatas($cleaned_datas);
            $change->setRequestedAt(new \DateTime());
            dump($change);
            $em->persist($change);
            $em->flush();

            $route = $classname . '::edit';
            // dd($route);
            return $this->redirectToRoute($route, ['product' => $instance->getId()]);
        }

        $view = $classname . 's/create.html.twig';

        return $this->render($view, [
            'form' => $form->createView(),
            'expected_change' => true
            ]
        );
    }

    /**
     * @Route(path="expected_change/execute", name="expected_change::execute", methods={"GET","POST"})
     *
     * @return Response
     * @throws \Exception
     */
    public function execute() {
        $em_admin = $this->getDoctrine()->getManager('default');
        $em_customer = $this->getDoctrine()->getManager('kfc');
        $rep_expect = $em_customer->getRepository(ExpectedChange::class);
        $rep_entity = $em_admin->getRepository(Entity::class);

        // $tasks = $rep_expect->findBy(['expectedAt' => new \DateTime()]);
        $tasks = $rep_expect->findAll(); // for test!
        $list = $rep_entity->findAll();
        $entities = [];

        foreach($list as $entity) {
            $index = $entity->getId();
            $entities[$index] = $entity->getName();
        }

        $entities[] = 'priceType';

        foreach($tasks as $task) {
            $id = $task->getEntity();
            $classname = $entities[$id];
            $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
            $rep = $em_customer->getRepository($entityPath);
            $datas = $task->getInstanceDatas();

            $instance = $rep->find($datas['id']);
            foreach ($datas as $property => $value) {
                if($property !== 'id') {
                    dump($property);
                    $method = 'set' . ucfirst($property);

                    if(method_exists($entityPath, $method)) {
                        $reflection = new \ReflectionMethod($entityPath, $method);
                        $arguments = $reflection->getParameters();
                        $field_type = $arguments[0]->getType()->getName();
                        dump($field_type);

                        if($field_type == 'DateTimeInterface') {
                            $date = new \DateTime();
                            $date->setTimestamp($value);
                            $value = $date;
                        }

                        if($field_type == 'string' && $value == null) {
                            $value = '';
                        }

                        if(strpos($field_type, 'App\Entity\Customer\\') === 0 && in_array($property, $entities)) {
                            $entityPathBis = "App\Entity\Customer\\" . ucfirst($property);
                            $rep = $em_customer->getRepository($entityPathBis);
                            $value = $rep->find($value['id']);
                            if($entityPath == 'App\Entity\Customer\Product' && $method == 'setPriceType') {
                                dump('pass');
                            }
                        }
                    }

                    $instance->$method($value);
                }
                //dump($instance);
            }
            /*
            $em_customer->persist($instance);
            $em_customer->flush();
            */
        }

        dd('finished');
        return new Response('datas updated');

    }

    /**
     * @param string $classname
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function getForms(string $classname, int $id): FormInterface
    {
        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        $em = $this->getDoctrine()->getManager('kfc');
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
        $instance = $em->getRepository($entityPath)->find($id);
        $instance_json = $this->buildJson($instance);

        // $entityForm = "App\Form\\" . ucfirst($classname) . 'Type';
        // $form = $this->createForm($entityForm, $instance);

        $rep_change = $em->getRepository(ExpectedChange::class);
        $all_change = $rep_change->findBy(['entity' => $entity->getId(), 'instance' => $id]);
        $all_dates = $this->getDoctrine()->getRepository(Date::class, 'kfc')->findAll();

        // dump($all_change);
        // dump($this->_selectedDates);

        $expectedChangesList = new ExpectedChangesList();
        $expectedChangesList->setCurrentObject($instance);

        $last_change = null; // bug contexte ==> Sélection de dates non utilisées pour l'instance courante (aucune modification), du coup last_change reste null!!
        foreach ($all_dates as $date) { // boucle sur toutes les dates obligatoires!!
            //dump($date->getValue()->format('y-m-d'));

                $exist = false;

                foreach($all_change as $change) {
                    // dump($change);
                    if($change->getExpectedAt() === $date) {
                        $last_change = $change;
                        $exist = true;

                        if(in_array($date, $this->_selectedDates)) {
                            $change = $this->hydrateEntityObject($change);
                            $expectedChangesList->addExpectedChange($change);
                        }
                    }
                }

                // dump($exist, in_array($date, $this->_selectedDates));

                if(!$exist && in_array($date, $this->_selectedDates)) {
                    // préparation du json avant hydratation
                    if($last_change !== null) {
                        $json = $last_change->getDatas();
                    } else {
                        $json = $instance_json;
                    }
                    $change = new ExpectedChange();
                    $change->setInstance($id);
                    $change->setEntity($entity->getId());
                    $change->setDatas($json);
                    $change->setExpectedAt($date);
                    $change->setRequestedAt(new \DateTime());
                    $change = $this->hydrateEntityObject($change);
                    $expectedChangesList->addExpectedChange($change);
                    // $em->persist($change);
                }

        }

        // dd($expectedChangesList);

        /*
        foreach($all_change as $change) {
            $change = $this->hydrateEntityObject($change);
            $expectedChangesList->addExpectedChange($change);

            // $change->setEntityObject();
            // $form = $this->createForm(ExpectedChangeType::class, $change, [
            //     'entityToChange' => new $entityForm
            // ]);
            // $date = $change->getExpectedAt()->getValue()->format('d-m-Y');
            // $forms[$date] = ['id' => $change->getId(), 'form' => $form];
        }
        */
        return $this->createForm(ExpectedChangesListType::class, $expectedChangesList);
    }

    /**
     * @param ExpectedChange $change
     * @return ExpectedChange
     * @throws \Exception
     */
    public function hydrateEntityObject(ExpectedChange $change): ExpectedChange {
        $em_admin = $this->getDoctrine()->getManager('default');
        $em_customer = $this->getDoctrine()->getManager('kfc');
        $rep_entity = $em_admin->getRepository(Entity::class);
        $list = $rep_entity->findAll();
        $entities = []; // Inclure cette variable en propriété de la classe!

        foreach($list as $entity) {
            $index = $entity->getId();
            $entities[$index] = $entity->getName();
        }

        $id = $change->getEntity();
        $classname = $entities[$id];
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);

        $datas = $change->getDatas();
        $instance = new $entityPath();

        foreach ($datas as $property => $value) {
            if($property !== 'id') {
                $method = 'set' . ucfirst($property); // Si recherche méthode get ==> pas de bug avec les collections...

                if(method_exists($entityPath, $method) && $property[strlen($property)-1] !== 's') {
                    $reflection = new \ReflectionMethod($entityPath, $method);
                    $arguments = $reflection->getParameters();
                    $field_type = $arguments[0]->getType()->getName();

                    if($field_type === 'DateTimeInterface') {
                        $date = new \DateTime();
                        $date->setTimestamp($value);
                        $value = $date;
                    }

                    if(strpos($field_type, 'App\Entity\Customer\\') === 0 && in_array($property, $entities)) {
                        $relatedEntityPath = "App\Entity\Customer\\" . ucfirst($property);
                        $rep = $em_customer->getRepository($relatedEntityPath);
                        $value = $rep->find($value);
                        /*
                        if($entityPath == 'App\Entity\Customer\Product' && $method == 'setPriceType') {
                            dump('pass');
                        }
                        */
                    }

                    /*
                    if($field_type === 'Doctrine\Common\Collections\Collection') {
                        $relatedEntity = substr(ucfirst($property), 0, -1);
                        $relatedEntityPath = "App\Entity\Customer\\" . $relatedEntity;
                        $rep = $em_customer->getRepository($relatedEntityPath);
                        $method = 'add' . $relatedEntity;
                        foreach($value as $item) {
                            $relatedEntityInstance = $rep->find($item);
                            $instance->$method($relatedEntityInstance);
                        }
                    }
                    */

                    $instance->$method($value);
                } else {
                    $method = 'add' . substr(ucfirst($property), 0, -1);
                    if(method_exists($entityPath, $method)) {
                        $relatedEntityPath = "App\Entity\Customer\\" . substr(ucfirst($property), 0, -1);
                        $em = $em_customer;
                        if(!class_exists($relatedEntityPath)) {
                            $relatedEntityPath = "App\Entity\Admin\\" . substr(ucfirst($property), 0, -1);
                            $em = $em_admin;
                        }
                        $rep = $em->getRepository($relatedEntityPath);
                        foreach($value as $item) {
                            $relatedEntityInstance = $rep->find($item);
                            $instance->$method($relatedEntityInstance);
                        }
                    }
                }
            }
        }
        $change->setEntityObject($instance);
        // dd($change);
        return $change;
    }

    /**
     * @Route(path="expected_change_bis/{classname}/{id}", name="expected_change::savebis", methods={"GET","POST"})
     *
     * @param Request $request
     * @param string $classname
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function savebis(Request $request, string $classname, int $id): Response
    {
        $serializedObject = $this->_session->get('serializedObject');
        // dump($serializedObject);
        // $deserializedObject = $this->__serializer->deserialize($serializedObject, ExpectedChangesList::class, 'json');
        $deserializedObject= json_decode($serializedObject);
        // dump($deserializedObject);

        $rep_date = $this->getDoctrine()->getRepository(Date::class, 'kfc');

        foreach($deserializedObject->expectedDates as $date) {
            $inst_date = $rep_date->find($date->id);
            $this->_selectedDates[] = $inst_date;
        }
        // No need currentObject!!

        // dd($this->_selectedDates);

        /*
        $selected_dates_ids = $request->request->get('selected_dates');
        $new_date = $request->request->get('new_date');

        if($selected_dates_ids !== null) {
            foreach ($selected_dates_ids as $date_id => $is_selected) {
                $inst_date = $rep_date->find($date_id);
                $this->_selectedDates[] = $inst_date;
            }
        } else {
            $this->_selectedDates = $rep_date->findAll();
        }

        if($new_date !== '') {
            $new = new Date();
            $new->setValue(new \DateTime($new_date));
            $this->_selectedDates[] = $new;
        }
        */

        // $this->_selectedDates = $rep_date->findAll();

        $em = $this->getDoctrine()->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        if($entity === null) {
            dd('Erreur: Aucune entité ' . $classname . ' n\'existe dans le system!');
        }

        $em = $this->getDoctrine()->getManager('kfc');
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
        $instance = $em->getRepository($entityPath)->find($id);
        $this->__memory = json_decode($this->__serializer->serialize($instance, 'json'),true);

        // dd($request);

        $form = $this->getForms($classname, $id); // $instance
        //dd(get_class($form));
        $form->handleRequest($request);
       //dd($form->getData());

        if($form->isSubmitted() && $form->isValid())
        {
            // Aucun traitement à faire sur le produit courant!! (flush)
           // dd($form->getData());
            foreach ($form->getData()->getExpectedChanges() as $change) {
                $json = $this->__serializer->serialize($change->getEntityObject(), 'json');
                $cleaned_datas = json_decode($json, true);

                $class_name = get_class($instance);
                $methods = get_class_methods($class_name);

                foreach ($methods as $method) {
                    $property = lcfirst(substr($method, 3));
                    if(substr($method, 0, 3) === 'get') {
                        $reflection = new \ReflectionMethod($entityPath, $method);
                        $field_type = $reflection->getReturnType()->getName();

                        if($field_type === 'DateTimeInterface') {
                            $date = $instance->$method();
                            $cleaned_datas[$property] = $date->getTimestamp();
                        }

                        if(strpos($field_type, 'App\Entity\Customer\\') === 0 ) {
                            $cleaned_datas[$property] = $instance->$method()->getId();
                        }

                        if($field_type === 'Doctrine\Common\Collections\Collection') {
                            $array = [];
                            foreach ($instance->$method() as $item) {
                                $array[] = $item->getId();
                            }
                            $cleaned_datas[$property] = $array;
                        }

                    }
                }

                // dd($this->__memory, $cleaned_datas);
                $change->setDatas($cleaned_datas);
                //dd($cleaned_datas);
                $change->setRequestedAt(new \DateTime());
            }
            $em->persist($change);
            // dd($change);
            $em->flush();
        }
        //dump(json_decode($this->__memory, true));

        return $this->render('customer/expected_change/create.html.twig', [
                'form' => $form->createView(),
                'dates' => $this->_selectedDates
            ]
        );
    }

    /**
     * @param $instance
     * @return array
     * @throws \Exception
     */
    public function buildJson($instance):array {
        // $instance = $change->getEntityObject();
        $json = $this->__serializer->serialize($instance, 'json');
        $cleaned_datas = json_decode($json, true);

        $classname = get_class($instance);
        $methods = get_class_methods($classname);

        foreach ($methods as $method) {
            $property = lcfirst(substr($method, 3));
            if(substr($method, 0, 3) === 'get') {
                $reflection = new \ReflectionMethod($classname, $method);
                $field_type = $reflection->getReturnType()->getName();

                if($field_type === 'DateTimeInterface') {
                    $date = $instance->$method();
                    $cleaned_datas[$property] = $date->getTimestamp();
                }

                if(strpos($field_type, 'App\Entity\Customer\\') === 0 ) {
                    $cleaned_datas[$property] = $instance->$method()->getId();
                }

                if($field_type === 'Doctrine\Common\Collections\Collection') {
                    $array = [];
                    foreach ($instance->$method() as $item) {
                        $array[] = $item->getId();
                    }
                    $cleaned_datas[$property] = $array;
                }

            }
        }
        // $change->setDatas($cleaned_datas);
        return $cleaned_datas;
    }

}