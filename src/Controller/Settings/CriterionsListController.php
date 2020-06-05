<?php

namespace App\Controller\Settings;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\CriterionsList;
use App\Form\Customer\CriterionsListType;
use App\Repository\Customer\CriterionsListRepository;
use App\Service\CriterionsListHandlerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/criterions_lists")
 */
class CriterionsListController extends AbstractController
{
    /**
     * @Route("/", name="criterions_lists_index", methods={"GET"})
     * @param CriterionsListRepository $criterionCategoryRepository
     * @return Response
     */
    public function index(CriterionsListRepository $criterionCategoryRepository): Response
    {
        return $this->render('settings/criterions_lists/index.html.twig', [
            'criterions_lists' => $criterionCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="criterions_lists_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session, CriterionsListHandlerService $criterionsListsHandler ): Response
    {

        $currentCustomer = $session->get( 'current_customer' ) ;
        $currentEM = $this->getDoctrine()->getManager( $currentCustomer->getName() ) ;


        $criterionsListsHandler->setWorkingEntityManager( $currentEM ) ;

        $criterionList = new CriterionsList();

        $criterionList->addCriterion( new Criterion() );
        $criterionList->addCriterion( new Criterion() );

        $form = $this->createForm(CriterionsListType::class, $criterionList);     
        
        $criterionListFormView = $form->createView();
        
        foreach($criterionListFormView->children["criterions"]->children as $index => $criterionForm){
            $criterionForm["selected"]->vars["label"] = $index === 0 ? 'Choix par defaut' : 'Choix n°'.($index + 1) ;
        }

        $form->handleRequest($request);

       
        if ($form->isSubmitted() && $form->isValid()) {
          
            
            if (
                $criterionsListsHandler->isCriterionsListNameAlreadyExistInDb( $criterionList ) ||
                ! $criterionsListsHandler->handleBasicCriterionInList( $criterionList ) ||
                ! $criterionsListsHandler->handleCriterionsInList( $criterionList ) ||
                ! $criterionsListsHandler->isMinimumCriterionsInListLimitIsReached( $criterionList )
            
            ) return $this->redirectToRoute( 'criterions_lists_new' ) ;
          //  dd( $criterionList );
            // dd($criterionList);
            $currentEM->persist($criterionList);
            $currentEM->flush();

            return $this->redirectToRoute('criterions_lists_index');
        }

        return $this->render('settings/criterions_lists/new.html.twig', [
            'criterions_list' => $criterionList,
            'form' => $criterionListFormView,
        ]);
    }

    /**
     * @Route("/{id}", name="criterions_lists_show", methods={"GET"})
     */
    public function show(CriterionsList $criterionList): Response
    {
        return $this->render('settings/criterions_lists/show.html.twig', [
            'criterions_list' => $criterionList,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="criterions_lists_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CriterionsList $criterionList): Response
    {
        $form = $this->createForm(CriterionsListType::class, $criterionList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('criterions_lists_index');
        }

        return $this->render('settings/criterions_lists/edit.html.twig', [
            'criterions_list' => $criterionList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="criterions_lists_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CriterionsList $criterionList): Response
    {
        if ($this->isCsrfTokenValid('delete'.$criterionList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($criterionList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('criterions_lists_index');
    }
}
