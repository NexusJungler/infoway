<?php


namespace App\Controller;

use App\Entity\Customer\PriceType;
use App\Form\PriceTypeType;
use App\Service\SessionManager;
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
    public function show(SessionManager $sessionManager): Response
    {
        $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
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
    public function create(Request $request, SessionManager $sessionManager): Response
    {
        $type = new PriceType();
        $form = $this->createForm(PriceTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
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
    public function edit(Request $request, PriceType $type, SessionManager $sessionManager): Response
    {
        $form = $this->createForm(PriceTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
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
    public function delete(Request $request, SessionManager $sessionManager): Response
    {
        $types = $request->request->get('types');
        if($types != []) {
            $em = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
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