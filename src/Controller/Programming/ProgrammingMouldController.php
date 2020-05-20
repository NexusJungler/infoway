<?php

namespace App\Controller\Programming;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Display;
use App\Entity\Customer\DisplaySetting;
use App\Entity\Customer\Media;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\Product;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Entity\Customer\Tag;
use App\Form\Customer\ProgrammingMouldType;
use App\Repository\Customer\ProgrammingMouldRepository;
use App\Serializer\Normalizer\EmptyDateTimeNormalizer;
use App\Serializer\Normalizer\IgnoreNotAllowedNulledAttributeNormalizer;
use App\Service\FlashBagHandler;
use Proxies\__CG__\App\Entity\Customer\Playlist;
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
class ProgrammingMouldController extends AbstractController
{
    /**
     * @Route("/", name="programming_programming_mould_index", methods={"GET"})
     */
    public function index(ProgrammingMouldRepository $ProgrammingMould): Response
    {
        return $this->render('programming/programming_mould/index.html.twig', [
            'programming_moulds' => $ProgrammingMould->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="programming_programming_mould_new", methods={"GET","POST"})
     */
    public function new(Request $request, FlashBagHandler $flashBagHandler, SerializerInterface $serializer, SessionInterface $session): Response
    {

        $entityManager = $this->getDoctrine()->getManager('kfc');
//        $serializedProgrammingMould = $flashBagHandler->getOneFlashBagOrNul('serializedProgrammingMould') ;
        $programmingMouldFromSession =  $session->get('programmingMould') ;

        $mediasPickables = [] ;

        $newProgrammingMould = new ProgrammingMould() ;

        $importedCriterionsIds = $programmingMouldFromSession->getCriterions()->map(function($criterion){
            return $criterion->getId();
        })->toArray() ;

        if( count( $importedCriterionsIds ) > 0 ) {

            $criterionsFromDb = $this->getDoctrine()->getRepository(Criterion::class)->findBy(['id' => $importedCriterionsIds] );

            foreach($criterionsFromDb as $criterionToAddToNewProgrammingMould){
                $newProgrammingMould->addCriterion( $criterionToAddToNewProgrammingMould );

                foreach($criterionToAddToNewProgrammingMould->getProducts() as $product){

                    foreach($product->getMedias() as $media){
                        if( ! in_array( $media , $mediasPickables ) ) $mediasPickables[] = $media ;
                    };
                }
            }
        }



        $importedTagsIds = $programmingMouldFromSession->getTags()->map( function( $tag ){
            return $tag->getId();
        })->toArray() ;

        if( count( $importedTagsIds ) > 0 ) {

            $tagsFromDb = $this->getDoctrine()->getRepository(Tag::class)->findBy(['id' => $importedCriterionsIds] );

            foreach($tagsFromDb as $tagToAddToNewProgrammingMould){
                $newProgrammingMould->addTag( $tagToAddToNewProgrammingMould );

                foreach( $tagToAddToNewProgrammingMould->getMedias() as $media ) {
                    if( ! in_array( $media , $mediasPickables ) ) $mediasPickables[] = $media ;
                }
            }
        }

        $displaySettingFromDb = $this->getDoctrine()->getRepository(DisplaySetting::class)->findOneBy( ['id' => $programmingMouldFromSession->getDisplaySetting()->getId() ] );
        $newProgrammingMould->setDisplaySetting( $displaySettingFromDb ) ;

        foreach( $programmingMouldFromSession->getTimeSlots() as $timeSlot ) {

            $newProgrammingMould->addTimeSlot( $timeSlot ) ;

            $mouldDisplay = new Display() ;
            $mouldDisplay->setTimeSlot( $timeSlot ) ;

            for( $i=1 ; $i<=$newProgrammingMould->getDisplaySetting()->getScreensQuantity(); $i++ ){
                $screenPlaylist  = new ScreenPlaylist() ;
                $screenPlaylistENtry = new ScreenPlaylistEntry();
                $screenPlaylistENtry->setMedia($this->getDoctrine()->getRepository(Media::class)->findOneBy(['id' => 4]));
                $screenPlaylistENtry->setPositionInPlaylist( 1 );
                $screenPlaylist->addEntry( $screenPlaylistENtry );
                $screenPlaylist->setScreenPosition( $i ) ;

                $mouldDisplay->addPlaylist( $screenPlaylist ) ;
            }

            $newProgrammingMould->addDisplay( $mouldDisplay ) ;
        }



//        dd( $programmingMouldToCreate ) ;


        //$entityManager->persist($programmingMouldToCreate);


        $optionsToPassToForm = [
            'allowDisplaySettingChoice' => false,
        ];


//        $productrepo = $this->getDoctrine()->getRepository(Product::class);
//
//        $product = $productrepo->findOneBy(['id' => 2]);
//        dd($product->getMedias()->getValues());


//        dd($programmingMouldToCreate);


        $form = $this->createForm(ProgrammingMouldType::class, $newProgrammingMould, $optionsToPassToForm );
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist( $newProgrammingMould );
            $entityManager->flush();


            return $this->redirectToRoute('programming_programming_mould_index');
        }

        return $this->render('programming/programming_mould/new.html.twig', [
            'programming_mould' => $newProgrammingMould,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_programming_mould_show", methods={"GET"})
     */
    public function show(ProgrammingMould $ProgrammingMould): Response
    {
        return $this->render('programming/programming_mould/show.html.twig', [
            'programming_mould' => $ProgrammingMould,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programming_programming_mould_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProgrammingMould $ProgrammingMould): Response
    {
        $form = $this->createForm(ProgrammingMouldType::class, $ProgrammingMould);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programming_programming_mould_index');
        }

        return $this->render('programming/programming_mould/edit.html.twig', [
            'programming_mould' => $ProgrammingMould,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_programming_mould_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProgrammingMould $ProgrammingMould): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ProgrammingMould->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ProgrammingMould);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programming_programming_mould_index');
    }
}
