<?php

namespace App\Controller\Programming;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\Display;
use App\Entity\Customer\DisplaySetting;
use App\Entity\Customer\Media;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\Product;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Entity\Customer\Tag;
use App\Form\Customer\AddMediaType;
use App\Form\Customer\ProgrammingMouldType;
use App\Repository\Customer\DisplaySettingRepository;
use App\Repository\Customer\DisplaySpaceRepository;
use App\Repository\Customer\ProgrammingMouldRepository;
use App\Serializer\Normalizer\EmptyDateTimeNormalizer;
use App\Serializer\Normalizer\IgnoreNotAllowedNulledAttributeNormalizer;
use App\Service\FlashBagHandler;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function new(Request $request, FlashBagHandler $flashBagHandler, SerializerInterface $serializer, SessionInterface $session, DisplaySpaceRepository $displaySpaceRepo ): Response
    {


        $entityManager = $this->getDoctrine()->getManager('kfc');
        $allDisplaySpacesName = $displaySpaceRepo->getAllDisplaySpacesNameIndexedById();

//        $serializedProgrammingMould = $flashBagHandler->getOneFlashBagOrNul('serializedProgrammingMould') ;
        $programmingMouldFromSession =  $session->get('programmingMould') ;

        $mediasPickables = new ArrayCollection() ;
        $mediasPickablesIndexedById = [] ;

        $newProgrammingMould = new ProgrammingMould() ;

        $importedCriterionsIds = $programmingMouldFromSession->getCriterions()->map(function($criterion){
            return $criterion->getId();
        })->toArray() ;

        $newProgrammingMould->setName( $programmingMouldFromSession->getName() );
        foreach( $programmingMouldFromSession->getAllowedMediasTypes() as $allowedMediaType ){
            $newProgrammingMould->addAllowedMediasType( $allowedMediaType );
        }
        if( count( $importedCriterionsIds ) > 0 ) {

            $criterionsFromDb = $this->getDoctrine()->getRepository(Criterion::class)->findBy(['id' => $importedCriterionsIds] );

            foreach($criterionsFromDb as $criterionToAddToNewProgrammingMould){
                $newProgrammingMould->addCriterion( $criterionToAddToNewProgrammingMould );

                foreach($criterionToAddToNewProgrammingMould->getProducts() as $product){

                    foreach($product->getMedias() as $media){
                        if( ! in_array( $media , $mediasPickablesIndexedById ) ) $mediasPickablesIndexedById[ $media->getId() ] = $media ;
                    };
                }
            }
        }



        $importedTagsIds = $programmingMouldFromSession->getTags()->map( function( $tag ){
            return $tag->getId();
        })->toArray() ;

        if( count( $importedTagsIds ) > 0 ) {

            $tagsFromDb = $this->getDoctrine()->getRepository(Tag::class)->findBy(['id' => $importedTagsIds] );

            foreach($tagsFromDb as $tagToAddToNewProgrammingMould){

                $newProgrammingMould->addTag( $tagToAddToNewProgrammingMould );
                foreach( $tagToAddToNewProgrammingMould->getMedias() as $media ) {
                    if( ! in_array( $media , $mediasPickablesIndexedById ) ) $mediasPickablesIndexedById[ $media->getId() ] = $media ;
                }
            }
        }

        $displaySettingFromDb = $this->getDoctrine()->getRepository(DisplaySetting::class)->findOneBy( ['id' => $programmingMouldFromSession->getDisplaySetting()->getId() ] );
        $newProgrammingMould->setDisplaySetting( $displaySettingFromDb ) ;
        $mouldDisplay = new Display();

        foreach( $programmingMouldFromSession->getTimeSlots() as $timeSlot ) {

            $entityManager->persist( $timeSlot );
            $newProgrammingMould->addTimeSlot( $timeSlot ) ;

            $broadcastSlot = new BroadcastSlot() ;
            $broadcastSlot->setTimeSlot( $timeSlot ) ;

            for( $i=1 ; $i<=$newProgrammingMould->getDisplaySetting()->getScreensQuantity(); $i++ ){
                $screenPlaylist  = new ScreenPlaylist() ;
                $screenPlaylistENtry = new ScreenPlaylistEntry();
                $screenPlaylistENtry->setMedia($this->getDoctrine()->getRepository(Media::class)->findOneBy(['id' => 1]));
                $screenPlaylistENtry->setPositionInPlaylist( 1 );
                $screenPlaylist->addEntry( $screenPlaylistENtry );
                $screenPlaylist->setScreenPosition( $i ) ;

                $broadcastSlot->addPlaylist( $screenPlaylist ) ;
                $mouldDisplay->addBroadcastSlot( $broadcastSlot );
            }

        }
        $newProgrammingMould->addDisplay( $mouldDisplay ) ;



//        dd( $programmingMouldToCreate ) ;


        //$entityManager->persist($programmingMouldToCreate);



        $optionsToPassToForm = [
            'allowDisplaySettingChoice' => false,
        ];
        $form = $this->createForm(ProgrammingMouldType::class, $newProgrammingMould, $optionsToPassToForm );
        $form->handleRequest($request);

//        $productrepo = $this->getDoctrine()->getRepository(Product::class);
//
//        $product = $productrepo->findOneBy(['id' => 2]);
//        dd($product->getMedias()->getValues());


//        dd($programmingMouldToCreate);
        $optionsToPassToAddMediasForm = [
            'mediasToDisplay' => new ArrayCollection( array_values( $mediasPickablesIndexedById ) ),
            'allowedMediasTypes' => $newProgrammingMould->getAllowedMediasTypes(),
            'screensQty' => $newProgrammingMould->getDisplaySetting()->getScreensQuantity(),
            'availablesTimeSlots' => $newProgrammingMould->getTimeSlots()
        ];

        $addMediaForm = $this->createForm(AddMediaType::class, [], $optionsToPassToAddMediasForm );


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist( $newProgrammingMould );
            $entityManager->flush();


            return $this->redirectToRoute('programming_programming_mould_index');
        }

        return $this->render('programming/programming_mould/new.html.twig', [
            'programmingMould' => $newProgrammingMould,
            'form' => $form->createView(),
            'addMediaForm' => $addMediaForm->createView(),
            'allDisplaySpacesName' => $allDisplaySpacesName ,
            'mediasPickables' => $mediasPickables
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
    public function edit(Request $request, ProgrammingMould $ProgrammingMould, DisplaySpaceRepository $displaySpaceRepo): Response
    {
        $form = $this->createForm(ProgrammingMouldType::class, $ProgrammingMould);
        $form->handleRequest($request);
        $allDisplaySpacesName = $displaySpaceRepo->getAllDisplaySpacesNameIndexedById();

        $mediasPickables = new ArrayCollection() ;
        $mediasPickablesIndexedById = [] ;

        foreach( $ProgrammingMould->getCriterions() as $criterion ){
            foreach($criterion->getProducts() as $product){

                foreach($product->getMedias() as $media){
                    if( ! in_array( $media , $mediasPickablesIndexedById ) ) $mediasPickablesIndexedById[ $media->getId() ] = $media ;
                };
            }
        }

        foreach( $ProgrammingMould->getTags() as $tag){
            foreach( $tag->getMedias() as $media ) {
                if( ! in_array( $media , $mediasPickablesIndexedById ) ) $mediasPickablesIndexedById[ $media->getId() ] = $media ;
            }
        }

        $optionsToPassToAddMediasForm = [
            'mediasToDisplay' => new ArrayCollection( array_values( $mediasPickablesIndexedById ) ),
            'allowedMediasTypes' => $ProgrammingMould->getAllowedMediasTypes(),
            'screensQty' => $ProgrammingMould->getDisplaySetting()->getScreensQuantity(),
            'availablesTimeSlots' => $ProgrammingMould->getTimeSlots()
        ];

        $addMediaForm = $this->createForm(AddMediaType::class, [], $optionsToPassToAddMediasForm );

        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager('kfc')->flush();

            return $this->redirectToRoute('programming_programming_mould_index');
        }

        return $this->render('programming/programming_mould/edit.html.twig', [
            'programmingMould' => $ProgrammingMould,
            'form' => $form->createView(),
            'allDisplaySpacesName' => $allDisplaySpacesName,
            'addMediaForm' => $addMediaForm->createView()
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
