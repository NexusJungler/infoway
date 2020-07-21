<?php

namespace App\Form\Customer;

use App\Entity\Admin\Allergen;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\ElementGraphic;
use App\Entity\Customer\PriceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                // 'input_format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'datepicker'
                ],
                'label' => 'Début de validité',
                'label_attr' => array('class' => 'label-custome')])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'datepicker'
                ],
                'label' => 'Fin de validité',
                'label_attr' => array('class' => 'label-custome')
                ])
            ->add('criterions', EntityType::class, [
                    'class' => Criterion::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'required' => false,
                    'label_attr' => array('class' => 'label-custome')
                ]
            )
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
                    'by_reference' => true
                ]
            )
            ->add('elements', EntityType::class, [
                    'class' => ElementGraphic::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'required' => false,
                    'label_attr' => array('class' => 'label-custome'),
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
            /*
            ->add('valider', SubmitType::class)
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
