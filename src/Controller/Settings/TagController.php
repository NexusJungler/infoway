<?php

namespace App\Controller\Settings;

use App\Entity\Admin\Customer;
use App\Entity\Admin\TagsList;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Form\Customer\TagListType;
use App\Form\Customer\TagsActionForm;
use App\Form\Customer\TagsActionType;
use App\Form\Customer\TagType;
use App\Object\Customer\Action\TagsAction;
use App\Repository\Customer\TagsRepository;
use App\Service\TagsHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tags")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", name="tags_index", methods={"GET" , "POST"})
     */
    public function index(TagsRepository $tagsRepository, Request $request): Response
    {
        $tagsList = new TagsAction() ;

        $tagsActionForm = $this->createForm(TagsActionType::class, $tagsList );
        $tagsActionForm->handleRequest($request);

        $tagsActionView = $tagsActionForm->createView();

        foreach($tagsActionView->children[ 'tags' ]->vars[ 'choices' ] as $choice ){
            $currentTag = $choice->data ;
            $tagsActionView->children['tags']->children[ $currentTag->getId() ]->vars['data'] = $currentTag ;
        }

        if ($tagsActionForm->isSubmitted() && $tagsActionForm->isValid()) {
            if( $tagsActionForm->get('delete')->isClicked() ) {
                return $this->delete( $tagsList , $request);
            }
            if( $tagsActionForm->get('edit')->isClicked() ) {
                if( $tagsList->getTags()->count() === 1 ) {
                    $tag = $tagsList->getTags()->get(0) ;

                    return $this->redirectToRoute( 'tags_edit', [ 'id' => $tag->getId() ]);
                }
            }
        }
        return $this->render('settings/tags/index.html.twig', [
            'tagsActionForm' => $tagsActionView ,
            'tags' => $tagsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tags_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session, Profiler $profiler, TagsHandler $tagsHandler): Response
    {

        $tagsList = new TagsList() ;
        $currentCustomer = $session->get('current_customer') ;
        $currentUser = $session->get('user') ;

        $tagExemple = new Tag() ;
        $tagsList->addTag( $tagExemple ) ;

        if( ! $currentCustomer instanceof Customer ) throw new \Error('invalid Customer') ;
        if( ! $currentUser instanceof User ) throw new \Error('invalid User') ;

        $customerManager = $this->getDoctrine()->getManager($currentCustomer->getName()) ;

        $datasToPassToTagForm = ['user' => $currentUser, 'customer' => $currentCustomer ] ;

//        dd($tagsList);


        $form = $this->createForm(TagListType::class, $tagsList , $datasToPassToTagForm);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(
                    ! $tagsHandler->isAllSitesSelectedArePossessedByUser( $tagsList->getSites(), $currentUser, $currentCustomer )
                ||  ! $tagsHandler->isMinimumSitesSelectionIsReached( $tagsList )
                ||  $tagsHandler->isTagsListEmpty( $tagsList )
            ) return $this->redirectToRoute('tags_new') ;


            foreach( $tagsList->getTags() as $tag ){

                foreach( $tagsList->getSites() as $site ){
                    $tag->addSite($site) ;
                }

                $customerManager->persist( $tag );
            }
            $customerManager->flush();

            return $this->redirectToRoute('tags_index');
        }

        return $this->render('settings/tags/new.html.twig', [
            'tagsList' => $tagsList,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="tags_show", methods={"GET"})
     */
    public function show(Tag $tag): Response
    {
        return $this->render('settings/tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tags_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tag $tag, SessionInterface $session): Response
    {

        $currentCustomer = $session->get('current_customer') ;
        $currentUser = $session->get('user') ;

        if( ! $currentCustomer instanceof Customer ) throw new \Error('invalid Customer') ;
        if( ! $currentUser instanceof User ) throw new \Error('invalid User') ;

        $customerManager = $this->getDoctrine()->getManager($currentCustomer->getName()) ;
        $customerSiteRepo = $customerManager->getRepository(Site::class) ;


        $sitesAffectedToTag  =  $tag->getSites()->getValues();
        $datasToPassToTagForm = ['user' => $currentUser, 'customer' => $currentCustomer ] ;

        $form = $this->createForm(TagType::class, $tag, $datasToPassToTagForm );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $sitesSelected = $tag->getSites() ;

            foreach($sitesSelected as $siteSelected){
                if ( ! in_array ($siteSelected , $sitesAffectedToTag) ) throw new \Error('One site was not affected , impossible to modify tag') ;
            }
            if( $sitesSelected->count() < 1 ) throw new \Error('You need to affect minimum one site to the tag created') ;

            $customerManager->flush();

            return $this->redirectToRoute('tags_index');
        }

        return $this->render('settings/tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="tags_delete", methods={"DELETE"})
     */
    public function delete(TagsAction $tagsToDelete , Request $request ): Response
    {
        $entityManager = $this->getDoctrine()->getManager('kfc');

        foreach($tagsToDelete->getTags() as $tag) {
            $entityManager->remove($tag);
        }
            $entityManager->flush();
//        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($tag);
//            $entityManager->flush();
//        }

        return $this->redirectToRoute('tags_index');
    }


}