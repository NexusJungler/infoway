<?php

namespace App\Form\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Display;
use App\Entity\Customer\ScreenPlaylist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BroadcastSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('playlists' , CollectionType::class, [
                'entry_type' => ScreenPlaylistType::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BroadcastSlot::class,
        ]);
    }
}
