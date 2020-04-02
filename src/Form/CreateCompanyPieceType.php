<?php

namespace App\Form;

use App\Entity\Admin\Country;
use App\Entity\Customer\CompanyPiece;
use App\Entity\Customer\CompanyPieceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;



class CreateCompanyPieceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),

            ])
            ->add('postal_code', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),
            ])
            ->add('phone_number', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),
            ])

            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label_attr' => array('class' => 'label-custome'),
            ])

            ->add('file', FileType::class, [
                'label' => 'Uploader un logo',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid logo',
                    ])
                ],
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyPiece::class,
        ]);
    }
}
