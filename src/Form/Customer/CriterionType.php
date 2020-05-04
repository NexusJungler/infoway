<?php

namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriterionType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('selected')
            ->add('products', EntityType::class , [
                'class' => Product::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'em' => 'kfc',
                'by_reference' => false
            ])
            ->add('sites', EntityType::class , [
                'class' => Site::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'em' => 'kfc' ,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Criterion::class,
            'em'        => 'kfc'
        ]);
        $resolver->setRequired([
            'em'
        ]) ;


    }
}
