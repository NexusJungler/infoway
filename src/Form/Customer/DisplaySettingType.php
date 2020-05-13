<?php

namespace App\Form\Customer;

use App\Entity\Customer\DisplaySetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplaySettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('screensNumber')
            ->add('startAt')
            ->add('endAt')
            ->add('playlists')
            ->add('allowedMediasTypes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DisplaySetting::class,
        ]);
    }
}
