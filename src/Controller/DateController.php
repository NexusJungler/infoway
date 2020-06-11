<?php


namespace App\Controller;

use App\Entity\Customer\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DateController extends AbstractController
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
     * @Route(path="ajax/date/list/{classname}/", name="dates::list", methods={"GET","POST"})
     *
     * @param string $classname
     * @return Response
     * @throws \Exception
     */
    public function getDatesList(string $classname): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $rep = $em->getRepository(Date::class);
        $dates = $rep->findAll();
        $json = $this->__serializer->serialize($dates,'json');

        return new Response($json);
    }

    /**
     * @Route(path="dates/manage", name="dates::manage", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function manage(Request $request): Response
    {

    }
}