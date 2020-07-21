<?php


namespace App\Controller;

use App\Entity\Customer\PriceType;
use App\Form\PriceTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PriceTypeController extends AbstractController
{
    /**
     * @Route(path="pricetypes/show", name="pricetypes::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $price_types = $em->getRepository(PriceType::class)->findAll();

        return $this->render("pricetypes/show.html.twig", [
            'types' => $price_types,
        ]);
    }

    /**
     * @Route(path="pricetype/create", name="pricetype::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $type = new PriceType();
        $form = $this->createForm(PriceTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
            $em->persist($type);
            $em->flush();
            return $this->redirectToRoute('pricetypes::show');
        }

        return $this->render("pricetypes/create.html.twig", [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route(path="pricetype/edit/{type}", name="pricetype::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param PriceType $type
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, PriceType $type): Response
    {
        $form = $this->createForm(PriceTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
            $em->flush();
            return $this->redirectToRoute('pricetypes::show');
        }

        return $this->render("pricetypes/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="pricetypes/delete", name="pricetypes::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $types = $request->request->get('types');
        if($types != []) {
            $em = $this->getDoctrine()->getManager('kfc');
            $rep = $em->getRepository( PriceType::class);
            foreach ($types as $id => $val) {
                $type = $rep->find($id);
                $em->remove($type);
                $em->flush();
            }
        }
        return $this->redirectToRoute('pricetypes::show');
    }

}