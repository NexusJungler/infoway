<?php

namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriterionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('selected', CheckboxType::class, [
                'attr' => ['class' => 'checkbox-custome'],
                'label'    => 'Choix',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label'    => ' ',
                'attr' => ['class' => 'input-custome']
            ])
            ->add('description',TextType::class, [
                'attr' => [
                    'class' => 'input-custome input-custome-desc ',
                    
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
