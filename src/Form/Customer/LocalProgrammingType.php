<?php

namespace App\Form\Customer;

use App\Entity\Customer\LocalProgramming;
use App\Entity\Customer\ProgrammingMould;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalProgrammingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mould', EntityType::class,
                [
                    'class' => ProgrammingMould::class,
                    'choice_label' => 'name'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocalProgramming::class,
        ]);
    }
}
