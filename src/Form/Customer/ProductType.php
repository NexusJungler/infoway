<?php

namespace App\Form\Customer;

use App\Entity\Admin\Allergen;
use App\Entity\Customer\PriceType;
use App\Repository\Admin\AllergenRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
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
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome')
            ])
            ->add('priceType', EntityType::class, [
                'class' => PriceType::class,
                'choice_label' => 'name',
                'label' => 'Type de prix',
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome')
            ])
            ->add('amount', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Quantité',
                'label_attr' => array('class' => 'label-custome')])
            ->add('description', TextareaType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome')])
            ->add('note', TextareaType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Mention',
                'label_attr' => array('class' => 'label-custome')])
            ->add('start', DateTimeType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Début de validité',
                'label_attr' => array('class' => 'label-custome')])
            ->add('end', DateTimeType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Fin de validité',
                'label_attr' => array('class' => 'label-custome')])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label_attr' => array('class' => 'label-custome')
                ]
            )
            ->add('allergens', EntityType::class, [
                    'class' => Allergen::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'required' => false,
                    'label_attr' => array('class' => 'label-custome'),
                    'by_reference' => false
                ]
            )
            ->add('logo', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Représentation graphique',
                'label_attr' => array('class' => 'label-custome')])
            /*
            ->add('tags', CollectionType::class, [
                    'entry_type' => TagType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false
                ]
            )
            */
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
