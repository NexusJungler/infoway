<?php

namespace App\Form;

use App\Entity\Admin\Customer;
use App\Entity\Customer\Role;
use App\Entity\Admin\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Nom",
                    'class' => 'input-group'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Email"
                ]
            ])
            ->add('login', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Utilisateur",
                    'class' => 'input-group'
                ]
            ])
            ->add('')
            ->add('phone_number', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Numero de telephone",
                    'class' => 'input-group'
                ]
            ])
            ->add('customer', CustomerType::class, [
                'data_class' => Customer::class,
               // 'choice_label' => 'name'
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
              //  'choice_label' => 'name'
            ]);

        // Adding an Event Listener
        // see : https://symfony.com/doc/current/form/dynamic_form_modification.html
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
        {

//            $user = $event->getData();
//            $form = $event->getForm();
//
//            // if user is new (e.g. hasn't been persisted to the database)
//            if(!$user || null == $user->getId())
//            {
//                $form->add('password', RepeatedType::class, [
//                    'label' => false,
//                    'type' =>PasswordType::class,
//                    'invalid_message' => 'Les mots de passes ne sont pas identique',
//                    'first_options'  => [
//                        'label' => false,
//                        'attr' => [
//                            'placeholder' => "Mot de passe"
//                        ]
//                    ],
//                    'second_options' => [
//                        'label' => false,
//                        'attr' => [
//                            'placeholder' => "Confirmation"
//                        ]
//                    ],
//                ]);
//            }
//
//            else
//            {
//                $form->add('password', TextType::class, [
//                    'label' => false,
//                    'attr' => [
//                        'placeholder' => "Mot de passe",
//                        'class' => 'input-group'
//                    ]
//                ]);
//            }

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
        ]);
    }

}
