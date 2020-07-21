<?php


namespace App\Controller\Customer;

use App\Entity\Customer\CheckoutSystem;
use App\Form\Customer\CheckoutSystemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutSystemController extends AbstractController
{
    /**
     * @Route(path="checkoutsystems/show", name="checkoutsystems::show", methods="GET")
     *
     * @return Response
     * @throws \Exception
     */
    public function show(): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        $checkoutSystems = $em->getRepository(CheckoutSystem::class)->findAll();

        return $this->render("checkoutsystems/show.html.twig", [
            'checkoutsystems' => $checkoutSystems
        ]);
    }

    /**
     * @Route(path="checkoutsystem/create", name="checkoutsystem::create", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $checkoutSystem = new checkoutSystem();

        $form = $this->createForm(CheckoutSystemType::class, $checkoutSystem);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
            $em->persist($checkoutSystem);
            $em->flush();
            return $this->redirectToRoute('checkoutsystems::show');
        }

        return $this->render("checkoutsystems/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="checkoutsystem/edit/{checkoutSystem}", name="checkoutsystem::edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param CheckoutSystem $checkoutSystem
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, CheckoutSystem $checkoutSystem): Response
    {
        $em = $this->getDoctrine()->getManager('kfc');
        // $checkoutSystems = $em->getRepository(CheckoutSystem::class)->findBy(['checkoutSystem' => $checkoutSystem]);

        $form = $this->createForm(CheckoutSystemType::class, $checkoutSystem);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager('kfc');
            $em->flush();
            return $this->redirectToRoute('checkoutsystems::show');
        }

        return $this->render("checkoutsystems/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="checkoutsystems/delete", name="checkoutsystems::delete", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request): Response
    {
        $systems = $request->request->get('systems');
        if($systems != []) {
            $em = $this->getDoctrine()->getManager('kfc');
            $rep = $em->getRepository( CheckoutSystem::class);
            foreach ($systems as $id => $val) {
                $system = $rep->find($id);
                $em->remove($system);
                $em->flush();
            }
        }
        return $this->redirectToRoute('checkoutsystems::show');
    }
}