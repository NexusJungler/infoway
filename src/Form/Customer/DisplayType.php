<?php

namespace App\Form\Customer;

use App\Entity\Customer\Display;
use App\Entity\Customer\ScreenPlaylist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('playlists' , CollectionType::class, [
                'entry_type' => ScreenPlaylistType::class,
            ])
            ->add('timeSlot', TimeSlotType::class, [

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Display::class,
        ]);
    }
}
