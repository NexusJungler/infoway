<?php

namespace App\Form\Customer;

use App\Entity\Customer\DisplaySetting;
use App\Entity\Customer\DisplaySpace;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplaySettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('screensQuantity');
//            ->add('displaySpace',EntityType::class, [
//                    'class' => DisplaySpace::class ,
//                    'choice_label' => 'name'
//                ]
//                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DisplaySetting::class,
        ]);
    }
}
