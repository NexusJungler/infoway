<?php

namespace App\Controller\Programming;

use App\Entity\Customer\DisplayMould;
use App\Entity\Customer\Product;
use App\Form\Customer\DisplayMouldType;
use App\Repository\Customer\DisplayMouldRepository;
use App\Serializer\Normalizer\EmptyDateTimeNormalizer;
use App\Serializer\Normalizer\IgnoreNotAllowedNulledAttributeNormalizer;
use App\Service\FlashBagHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/programming/display-mould")
 */
class DisplayMouldController extends AbstractController
{
    /**
     * @Route("/", name="programming_display_mould_index", methods={"GET"})
     */
    public function index(DisplayMouldRepository $displayMould): Response
    {
        return $this->render('programming/display_mould/index.html.twig', [
            'display_moulds' => $displayMould->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="programming_display_mould_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashBagHandler $flashBagHandler, SerializerInterface $serializer, SessionInterface $session): Response
    {


        $serializedDisplayMould = $flashBagHandler->getOneFlashBagOrNul('serializedDisplayMould') ;

        $serializedDisplayMould =  $session->get('serializedDisplayMould', $serializedDisplayMould) ;

        if( $serializedDisplayMould === null ) throw new \Error( 'invalid mould datas') ;

        $mediasPickables = [] ;



        $displayMouldToCreate = $serializer->deserialize( $serializedDisplayMould ,DisplayMould::class,'json');
//        dd($displayMouldToCreate);

//        dd( $displayMouldToCreate ) ;
        $entityManager = $this->getDoctrine()->getManager('kfc');
        $entityManager->persist($displayMouldToCreate);


        $optionsToPassToForm = [
            'allowDisplaySpaceChoice' => false,
        ];


        $productrepo = $this->getDoctrine()->getRepository(Product::class);

        $product = $productrepo->findOneBy(['id' => 2]);
//        dd($product->getMedias()->getValues());

        foreach( $displayMouldToCreate->getCriterions() as $criterion ){
            foreach($criterion->getProducts() as $product){
                foreach($product->getMedias() as $media){
                    if( ! in_array( $media , $mediasPickables ) ) $mediasPickables[] = $media ;
                };
            }
        }

      foreach( $displayMouldToCreate->getTags() as $tag ) {
          foreach( $tag->getMedias() as $media ) {
              if( ! in_array( $media , $mediasPickables ) ) $mediasPickables[] = $media ;
          }
      }
        $form = $this->createForm(DisplayMouldType::class, $displayMouldToCreate, $optionsToPassToForm );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($displayMouldToCreate);
            $entityManager->flush();


            return $this->redirectToRoute('programming_display_mould_index');
        }

        return $this->render('programming/display_mould/new.html.twig', [
            'display_mould' => $displayMouldToCreate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_mould_show", methods={"GET"})
     */
    public function show(DisplayMould $displayMould): Response
    {
        return $this->render('programming/display_mould/show.html.twig', [
            'display_mould' => $displayMould,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programming_display_mould_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DisplayMould $displayMould): Response
    {
        $form = $this->createForm(DisplayMouldType::class, $displayMould);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programming_display_mould_index');
        }

        return $this->render('programming/display_mould/edit.html.twig', [
            'display_mould' => $displayMould,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_mould_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DisplayMould $displayMould): Response
    {
        if ($this->isCsrfTokenValid('delete'.$displayMould->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($displayMould);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programming_display_mould_index');
    }
}
