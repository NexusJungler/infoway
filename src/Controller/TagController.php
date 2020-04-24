<?php

namespace App\Controller;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Form\Customer\TagType;
use App\Repository\Customer\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="tags_index", methods={"GET"})
     */
    public function index(TagsRepository $tagsRepository): Response
    {
        return $this->render('tags/index.html.twig', [
            'tags' => $tagsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tags_new", methods={"GET","POST"})
     */
    public function new(Request $request, SessionInterface $session, Profiler $profiler): Response
    {

        $tag = new Tag() ;
        $currentCustomer = $session->get('current_customer') ;
        $currentUser = $session->get('user') ;


        if( ! $currentCustomer instanceof Customer ) throw new \Error('invalid Customer') ;
        if( ! $currentUser instanceof User ) throw new \Error('invalid User') ;

        $customerManager = $this->getDoctrine()->getManager($currentCustomer->getName()) ;
        $customerSiteRepo = $customerManager->getRepository(Site::class) ;


        $sitesPossessedByCustomer =  $customerSiteRepo->getSitesByUserAndCustomer($currentUser,$currentCustomer) ;
        $datasToPassToTagForm = ['sites' =>$sitesPossessedByCustomer ] ;

        $form = $this->createForm(TagType::class, $tag , ['sites' => $datasToPassToTagForm]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerManager->persist($tag);
            $customerManager->flush();

            return $this->redirectToRoute('tags_index');
        }

        return $this->render('tags/new.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tags_show", methods={"GET"})
     */
    public function show(Tag $tag): Response
    {
        return $this->render('tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tags_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tag $tag): Response
    {

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tags_index');
        }

        return $this->render('tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tags_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tags_index');
    }
}
