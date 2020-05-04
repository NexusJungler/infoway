<?php

namespace App\Controller\Settings;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use App\Form\Customer\CriterionType;
use App\Repository\Customer\CriterionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/criterions")
 */
class CriterionController extends AbstractController
{


    /**
     * @Route("/{id}", name="criterions_show", methods={"GET"})
     */
    public function show(Criterion $criterion): Response
    {
        return $this->render('customer/criterion/show.html.twig', [
            'criterion' => $criterion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="criterions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Criterion $criterion, SessionInterface $session): Response
    {
        $customerEM = $this->getDoctrine()->getManager('kfc') ;

        $currentCustomer = $session->get('current_customer') ;

        $siteRepo = $customerEM->getRepository(Site::class ) ;
        $productRepo = $customerEM->getRepository(Product::class) ;

        $criterionsSites =  $criterion->getSites() ;
        $availablesSites = $siteRepo->getAllSitesWhereCriterionNotAppear( $criterion ) ;
        $availablesProducts = $productRepo->getAllProductsWhereCriterionDoesNotAppear($criterion) ;



        $form = $this->createForm(CriterionType::class, $criterion, [
//                'em' => $currentCustomer->getName()
            ]
        );

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('criterions_show', ['id' => $criterion->getId()]);
        }

        return $this->render('criterions/edit.html.twig', [
            'criterion' => $criterion,
            'form' => $form->createView(),
            'availablesSites' => $availablesSites,
            'availablesProducts' => $availablesProducts
        ]);
    }

    /**
     * @Route("/{id}", name="criterions_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Criterion $criterion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$criterion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($criterion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('criterions_index');
    }
}
