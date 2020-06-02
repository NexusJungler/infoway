<?php

namespace App\Form\Customer;

use App\Entity\Customer\CheckoutSystem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutSystemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'input-custome'
            ],
            'label' => 'Nom',
            'label_attr' => array('class' => 'label-custome')])

        ->add('valider', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CheckoutSystem::class
        ]);
    }
}



