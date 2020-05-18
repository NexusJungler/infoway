<?php

namespace App\Controller\Programming;

use App\Entity\Customer\DisplaySetting;
use App\Entity\Customer\DisplaySpace;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\TimeSlot;
use App\Form\Customer\DisplaySettingType;
use App\Form\Customer\ProgrammingMouldType;
use App\Repository\Customer\DisplaySettingRepository;
use App\Service\FlashBagHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/programming/display-setting")
 */
class DisplaySettingController extends AbstractController
{
    /**
     * @Route("/", name="programming_display_setting_index", methods={"GET"})
     */
    public function index(DisplaySettingRepository $displaySettingRepository): Response
    {
        return $this->render('programming/display_setting/index.html.twig', [
            'display_settings' => $displaySettingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{displaySpace}", name="programming_display_setting_new", methods={"GET","POST"})
     */
    public function new(Request $request, DisplaySpace $displaySpace): Response
    {
        $displaySetting = new DisplaySetting();
        $form = $this->createForm(DisplaySettingType::class, $displaySetting);
        $displaySetting->setDisplaySpace( $displaySpace ) ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager('kfc');
            $entityManager->persist($displaySetting);
            $entityManager->flush();

            return $this->redirectToRoute('programming_display_setting_index');
        }

        return $this->render('programming/display_setting/new.html.twig', [
            'display_setting' => $displaySetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_setting_show", methods={"GET"})
     */
    public function show(Request $request , DisplaySetting $displaySetting, SerializerInterface $serializer, FlashBagHandler $flashBagHandler, SessionInterface $session): Response
    {  $ProgrammingMould = new ProgrammingMould() ;
        $ProgrammingMould->setDisplaySetting( $displaySetting ) ;

        $defaultTimeSlot = new TimeSlot() ;
        $defaultTimeSlot->setStartAt(new \DateTime('00:00'));
        $defaultTimeSlot->setEndAt(new \DateTime('00:00'));

        $ProgrammingMould->addTimeSlot( $defaultTimeSlot ) ;

        $optionsToPassToForm = [
            'allowPlaylistCreation' => false,
            'allowDisplaySpaceChoice' => false,
            'allowModelChoice'   => true
        ];

        $form = $this->createForm(ProgrammingMouldType::class, $ProgrammingMould, $optionsToPassToForm );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handleCircularRefContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                    return $object->getId();
                },
            ];


            $serializedProgrammingMould = $serializer->serialize($ProgrammingMould,'json' , $handleCircularRefContext) ;
//            dd($serializedProgrammingMould);


            $session->set('serializedProgrammingMould', $serializedProgrammingMould) ;
            $flashBagHandler->getFlashBagContainer()->add('serializedProgrammingMould',$serializedProgrammingMould ) ;

            //dd($flashBagHandler);
//            $flashBagHandler->addFlashBag('serializedProgrammingMould' , $serializedProgrammingMould);



            return $this->redirectToRoute('programming_programming_mould_new');
        }
        return $this->render('programming/display_setting/show.html.twig', [
            'display_setting' => $displaySetting,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programming_display_setting_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DisplaySetting $displaySetting): Response
    {
        $form = $this->createForm(DisplaySettingType::class, $displaySetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programming_display_setting_index');
        }

        return $this->render('programming/display_setting/edit.html.twig', [
            'display_setting' => $displaySetting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programming_display_setting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DisplaySetting $displaySetting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$displaySetting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($displaySetting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programming_display_setting_index');
    }
}
