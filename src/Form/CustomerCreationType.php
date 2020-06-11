<?php


namespace App\Form;


use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\TimeZone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class CustomerCreationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', TextType::class, [

                ])
                ->add('address', TextType::class, [

                ])
                ->add('logoFile', FileType::class, [
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
                ->add('postalCode', TextType::class, [

                ])
                ->add('country', EntityType::class, [
                    'class' => Country::class,
                    'choice_label' => 'name'
                ])
                ->add('city', TextType::class, [

                ])
                ->add('timezone', EntityType::class, [
                    'class' => TimeZone::class,
                    'choice_label' => 'name'
                ])
                ->add('description', TextareaType::class, [

                ])

        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }

}