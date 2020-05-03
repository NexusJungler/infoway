<?php


namespace App\Controller;

use App\Entity\Customer\ExpectedChange;
use App\Entity\Admin\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

class ExpectedChangeController extends AbstractController
{

    private $__serializer;
    private $__memory;

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
     * @Route(path="expected_change/{classname}/{id}", name="expected_change::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @param string $classname
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function save(Request $request, string $classname, int $id): Response
    {
        $change = new ExpectedChange();
        $change->setRequestedAt(new \DateTime());

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

        $form = $this->createForm($entityForm, $instance);
        $form->handleRequest($request);

        //dump(json_decode($this->__memory, true));

        if($form->isSubmitted() && $form->isValid())
        {
            $expected_date = $request->request->get('expected_date');
            $route = $classname . '::edit';
            $json = $this->__serializer->serialize($instance, 'json');

            $cleaned_datas = json_decode($json, true);
            $cleaned_datas['createdAt'] = $cleaned_datas['createdAt']['timestamp'];
            $cleaned_datas['start'] = $cleaned_datas['start']['timestamp'];
            $cleaned_datas['end'] = $cleaned_datas['end']['timestamp'];

            dd($this->__memory, $cleaned_datas);

            $change->setEntity($entity->getId());
            $change->setInstanceDatas($cleaned_datas);
            $change->setExpectedAt(new \DateTime($expected_date)); // remplacer par date::class!!

            $em->persist($change);
            $em->flush();
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


}