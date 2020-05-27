<?php

namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriterionInListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('selected',CheckboxType::class,[
                'label' => 'choix n°__name__',
                'attr'=> [
                    'class' => 'checkbox-criterion'
                ],
            ] )
            ->add('name', TextType::class, [
                'label' => false,
                'attr'=> [
                    'class' => 'input-custome'
                ],
                
            ])
            ->add('description', TextAreaType::class,[
                'label' => false,
                'attr'=> [
                    'class' => 'input-custome-desc'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Criterion::class,
        ]);
    }
}
