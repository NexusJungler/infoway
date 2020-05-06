<?php


namespace App\Form;


use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'media_name',
                ]
            ])

            ->add('diffusionStart', DateType::class, [
                'label' => 'Début de diffusion',
                'attr' => [
                    'class' => 'media_diffusion_date_start',
                ]
            ])

            ->add('diffusionEnd', DateType::class, [
                'label' => 'Fin de diffusion',
                'attr' => [
                    'class' => 'media_diffusion_date_end',
                ]
            ])

            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'media_tags'
                ]
            ])

            ->add('products', EntityType::class, [
                'label' => 'Produits',
                'class' => Product::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'media_products'
                ]
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