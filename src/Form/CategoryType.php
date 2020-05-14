<?php

namespace App\Form;

use App\Entity\Customer\Category;
use App\Entity\Customer\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CategoryType extends AbstractType
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

            ->add('note', TextareaType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Mention',
                'label_attr' => array('class' => 'label-custome')])
            ->add('logo', TextType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Représentation graphique',
                'label_attr' => array('class' => 'label-custome')])

            /*
            ->add('products', CollectionType::class, [
                'entry_type' => ProductType::class,
                'allow_add' => true,
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            */
            ->add('valider', SubmitType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}

