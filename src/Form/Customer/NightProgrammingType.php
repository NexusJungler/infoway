<?php

namespace App\Form\Customer;

use App\Entity\Customer\NightProgramming;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NightProgrammingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priceIncrease')
            ->add('startAt')
            ->add('endAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NightProgramming::class,
        ]);
    }
}
