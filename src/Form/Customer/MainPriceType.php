<?php

namespace App\Form\Customer;

use App\Entity\Customer\MainPrice;
use App\Entity\Customer\PricesFactory;
use App\Entity\Customer\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\File;

class MainPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            /*
            ->add('factory', EntityType::class, [
                'class' => PricesFactory::class,
                'choice_label' => 'name',
                'label' => 'groupe prix',
                'attr' => [
                    'class' => 'input-custome',
                    'disabled' => false
                ],

            ])
            */
            /*
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'produit',
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome')
            ])
            */

            ->add('dayValue', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'prix Jour',
                'label_attr' => array('class' => 'label-custome')])

            ->add('nightValue', TextType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'prix Nuit',
                'label_attr' => array('class' => 'label-custome')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MainPrice::class
        ]);
    }
}

