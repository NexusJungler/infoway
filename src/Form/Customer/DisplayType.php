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
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('broadcastSlots', CollectionType::class, [
                'entry_type' => BroadcastSlotType::class,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [ $this, 'onPreSetDataGenerateTimeSlotsSelector' ] );
    }

    public function onPreSetDataGenerateTimeSlotsSelector(FormEvent $event)
    {
        $currentDisplay = $event->getData();
        $form = $event->getForm();

        $form->add('timeSlots', ChoiceType::class, [
            'label' => 'Creneaux',
            'choices' => $currentDisplay->getBroadcastSlots()->map( fn( BroadcastSlot $broadcastSlot ) => $broadcastSlot->getTimeSlot() )->toArray() ,
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
    }
}