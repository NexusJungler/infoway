<?php

namespace App\Controller\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\CriterionList;
use App\Form\Customer\CriterionListType;
use App\Repository\Customer\CriterionCategoryRepository;
use App\Repository\Customer\CriterionListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/criterion/list")
 */
class CriterionListController extends AbstractController
{
    /**
     * @Route("/", name="criterion_list_index", methods={"GET"})
     */
    public function index(CriterionListRepository $criterionCategoryRepository): Response
    {
        return $this->render('criterion_list/index.html.twig', [
            'criterion_lists' => $criterionCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="criterion_list_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $criterionList = new CriterionList();


        $criterionList->addCriterion( new Criterion() );
        $criterionList->addCriterion( new Criterion() );

        $form = $this->createForm(CriterionListType::class, $criterionList);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $criterionPositionInlist = 0 ;
            foreach($criterionList->getCriterions() as $criterion){
                if ( $criterion->getName() === null ) {
                    $criterionList->removeCriterion($criterion) ;
                } else{
                    $criterionPositionInlist ++ ;
                    $criterion->setPosition( $criterionPositionInlist ) ;

                }

            }

            $nbOfCriterionToAddInBase = count ( $criterionList->getCriterions()  ) ;
            if( $nbOfCriterionToAddInBase < 2 ) throw new \Error('Minimum 2 criterions unreached') ;


            $entityManager = $this->getDoctrine()->getManager('kfc');
            $entityManager->persist($criterionList);
            $entityManager->flush();

            return $this->redirectToRoute('criterion_list_index');
        }

        return $this->render('criterion_list/new.html.twig', [
            'criterion_lists' => $criterionList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="criterion_list_show", methods={"GET"})
     */
    public function show(CriterionList $criterionList): Response
    {
        return $this->render('criterion_list/show.html.twig', [
            'criterion_list' => $criterionList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="criterion_list_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CriterionList $criterionList): Response
    {
        $form = $this->createForm(CriterionListType::class, $criterionList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('criterion_list_index');
        }

        return $this->render('criterion_list/edit.html.twig', [
            'criterion_list' => $criterionList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="criterion_list_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CriterionList $criterionList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$criterionList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($criterionList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('criterion_list_index');
    }
}
