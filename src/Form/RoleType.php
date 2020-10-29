<?php


namespace App\Form;


use App\Entity\Customer\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'role_name',
            ]
        ])

            ->add('description', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'role_description',
                ]
            ])

            ->add('level', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'role_level',
                ]
            ])

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Role::class,
       ]);
    }

}