<?php


namespace App\Service;

use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Admin\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

class ExpectedChangeService
{

    private $__serializer;
    private $__memory;
    private $__register;

    public function __construct(ManagerRegistry $register)
    {
        $this->__register = $register;
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

    public function save(Request $request, string $classname, int $id): Response
    {
        $em = $this->__register->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        if($entity === null) {
            dd('Erreur: Aucune entité ' . $classname . ' n\'existe dans le system!');
        }

        $em = $this->__register->getManager('kfc');
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

            dump($cleaned_datas);
            // dd($this->__memory, $cleaned_datas);
            $change->setDatas($cleaned_datas);
            $change->setRequestedAt(new \DateTime());
            dd($change);
            $em->persist($change);
            $em->flush();

            $route = $classname . '::edit';
            // dd($route);
            return $this->redirectToRoute($route, ['id' => $instance]);
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
        $em_admin = $this->__register->getManager('default');
        $em_customer = $this->__register->getManager('kfc');
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
    public function getForms(string $classname, int $id): Response
    {
        $forms = []; $dates = [];
        $em = $this->__register->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        $em = $this->__register->getManager('kfc');
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
        $entityForm = "App\Form\\" . ucfirst($classname) . 'Type';

        $instance = $em->getRepository($entityPath)->find($id);
        $rep_change = $em->getRepository(ExpectedChange::class);

        $all_change = $rep_change->findBy(['entity' => $entity->getId(), 'instance' => $id]);

        foreach($all_change as $change) {
            // dump($change);
            $form = $this->createForm($entityForm, $instance);
            $forms[] = $form;
            $dates[] = $change->getExpectedAt();
        }
        dump($dates, $forms);
        $datas = ['dates' => $dates, 'forms' => $forms];

        return new JsonResponse($datas); // Service à implémenter!
    }


    public function update(Request $request, string $classname, int $id): Response
    {
        $em = $this->__register->getManager('default');
        $entity = $em->getRepository(Entity::class)->findOneBy(['name' => $classname]);

        $em = $this->__register->getManager('kfc');
        $entityPath = "App\Entity\Customer\\" . ucfirst($classname);
        $instance = $em->getRepository($entityPath)->find($id);

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

        //dd($change);
        $em->persist($change);
        $em->flush();

        return new Response();

    }


}