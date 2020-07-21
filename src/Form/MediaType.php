<?php


namespace App\Form;


use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'media_id',
                    'value' => '__MEDIA_ID__',
                    'disabled' => true,
                ]
            ])

            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'media_name',
                    'value' => '__MEDIA_NAME__',
                    'disabled' => true,
                ]
            ])

            /*->add('mediaType', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'media_type',
                    'value' => '__MEDIA_TYPE__',
                    'disabled' => true,
                ]
            ])*/

            ->add('extension', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'media_extension',
                    'value' => '__MEDIA_EXTENSION__',
                    'disabled' => true,
                ]
            ])

            ->add('diffusionStart', DateTimeType::class, [
                'label' => 'Du',
                'widget' => 'single_text',
                'input_format' => 'd-m-Y H:i',
                'attr' => [
                    'class' => 'media_diffusion_date_start',
                ]
            ])

            ->add('diffusionEnd', DateTimeType::class, [
                'label' => 'Au',
                'widget' => 'single_text',
                'input_format' => 'd-m-Y H:i',
                'attr' => [
                    'class' => 'media_diffusion_date_end',
                ]
            ])

            ->add('tags', EntityType::class, [
                'label' => false,
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'media_tags',
                ]
            ])

            ->add('products', EntityType::class, [
                'label' => false,
                'class' => Product::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'media_products',
                ]
                ])

            ->add('containIncruste', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'media_contain_incruste',
                ],
                'choices' => [
                  'Oui' => true,
                  'Non' => false,
                ],
                'multiple'=> false,
                'expanded'=> true,
                'data' => false,
                'mapped' => false,
            ])

            // @TODO: incrustes
            //->add('incrustes')

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }

}