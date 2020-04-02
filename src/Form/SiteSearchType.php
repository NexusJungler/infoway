<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\SiteSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse'
                ]
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ville'
                ]
            ])
            ->add('country', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Country::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteSearch::class,
            'translation_domain' => 'forms',
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
