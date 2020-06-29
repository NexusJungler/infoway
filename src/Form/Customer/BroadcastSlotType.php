<?php

namespace App\Form\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Display;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\TimeSlot;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BroadcastSlotType extends AbstractType
{
    private ArrayCollection $timeSlotsChoices ;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->timeSlotsChoices = $options['timeSlotsChoices'] ;
        $this->timeSlotsChoices = $this->timeSlotsChoices->filter( fn( TimeSlot $timeSlot ) => $timeSlot );


        $builder
            ->add('playlists' , CollectionType::class, [
                'entry_type' => ScreenPlaylistType::class,
            ])
            ->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choices' => $this->timeSlotsChoices,
                'choice_label' => 'name'
            ])
        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BroadcastSlot::class,
            'timeSlotsChoices' => new ArrayCollection()
        ]);
        $resolver->setRequired([
            'timeSlotsChoices'
        ]);
    }
}
