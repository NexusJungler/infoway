<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Customer;
use App\Entity\TimeZone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => "Nom"
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'placeholder' => "Adresse"
                ]
            ])
            ->add('postal_code', TextType::class, [
                'attr' => [
                    'placeholder' => "Code postale"
                ]
            ])
            ->add('phone_number', TextType::class, [
                'attr' => [
                    'placeholder' => "Numero de telephone"
                ]
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name'
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => "Ville"
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Description",
                    'rows' => '30',
                    'cols' => '150',
                ]
            ])
            ->add('time_zone', EntityType::class, [
                'class' => TimeZone::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'translation_domain' => 'forms',
        ]);
    }
}
