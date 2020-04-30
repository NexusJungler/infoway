<?php


namespace App\Controller;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
//use App\Normalizer\ApiNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @var Serializer
     */
    private $__serializer;

    public function __construct() {

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__']
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $this->__serializer = new Serializer([$normalizer], [$encoder]);
    }

    /**
     * @Route(path="/api/get/{entity}/datas", name="api::getAllDatasFromEntity", methods="GET",
     *     requirements={"entity": "[a-zA-Z]+"})
     *
     * @param string $entity
     * @return Response
     * @throws \Exception
     */
    public function getAllDatasFromEntity(string $entity): Response
    {

        $entity = "App\Entity\Admin\\" . $entity;

        if(!class_exists($entity))
            throw new \Exception(sprintf("Internal error : cannot find class '%s'", $entity));


        $class = new $entity();

        $repo = $this->getDoctrine()->getManager('default')->getRepository(get_class($class));

        $datas = $repo->findAll();

        $json = $this->__serializer->serialize($datas,'json');

        return new Response(json_encode($json));

    }


    /**
     * @Route(path="/api/get/{entity}/{data_id}", name="api::getDataFromEntityById", methods="GET",
     *     requirements={"entity": "[a-zA-Z]+", "data_id": "\d+"})
     *
     * @param string $entity
     * @param int $data_id
     * @return Response
     * @throws \Exception
     */
    public function getDataFromEntityById(string $entity, int $data_id): Response
    {

        $entity = "App\Entity\Admin\\" . ucfirst($entity);

        if(!class_exists($entity))
            throw new \Exception(sprintf("Internal error : cannot find class '%s'", $entity));

        $class = new $entity();

        $repo = $this->getDoctrine()->getManager('default')->getRepository(get_class($class));

        $data = $repo->findOneById($data_id);
        if($data === null)
            $data = [];

        $json = $this->__serializer->serialize($data,'json');

        dd($json);

        /*
        $json = json_decode($json, true);
        foreach ($json as $i => $property) {
            if(is_array($property)) {
                foreach ($property as $j => $titi) {
                    if(strpos($j, '_') !== false) {
                        unset($json[$i][$j]);
                    }
                }
            }
        }
        */
        return new Response($json);
    }


}