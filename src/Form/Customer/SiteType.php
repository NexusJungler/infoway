<?php

namespace App\Form\Customer;

use App\Entity\Customer\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('adress')
            ->add('postalCode')
            ->add('city')
            ->add('phoneNumber')
            ->add('observations')
            ->add('countryId')
            ->add('timezoneId')
            ->add('customerId')
            ->add('devise')
            ->add('criterions')
            ->add('tags')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }
}
