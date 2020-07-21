<?php

namespace App\Form\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Display;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayType extends AbstractType
{
    private $programming  ;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $programming = $options['programming'] ;


        $builder
            ->add('broadcastSlots', CollectionType::class, [
                'entry_type' => BroadcastSlotType::class,
                'allow_add' => true,
                'entry_options' => [
                    'timeSlotsChoices' => $programming->getTimeSlots()
                ]
            ])
            ->add('startAt', DateType::class, [
                'label' => 'Du',
                'widget' => 'single_text',
                'html5' => false
            ] )
            ->add('endAt', DateType::class, [
                'label' => 'au',
                'widget' => 'single_text',
                'html5'   => false
            ] )
            ->addEventListener(FormEvents::PRE_SET_DATA, [ $this, 'onPreSetDataGenerateTimeSlotsSelector' ] );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->children['broadcastSlots']->vars['display_prototype'] = true ;
    }

    public function onPreSetDataGenerateTimeSlotsSelector(FormEvent $event)
    {

        $currentDisplay = $event->getData();
        $choices = ( $currentDisplay !== null ) ? $currentDisplay->getBroadcastSlots()->map( fn( BroadcastSlot $broadcastSlot ) => $broadcastSlot->getTimeSlot() )->toArray() : [] ;

        $form = $event->getForm();

        $form->add('timeSlots', ChoiceType::class, [
            'label' => 'Creneaux',
            'choices' => $choices ,
            'choice_label' => fn(TimeSlot $timeSlot) => $timeSlot->getStartAt()->format('H:i') . ' - ' . $timeSlot->getEndAt()->format('H:i') ,
            'mapped' => false,
            'multiple' => true,
            'expanded' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Display::class,
        ]);
        $resolver->setRequired([
            'programming'
        ]);
    }
}