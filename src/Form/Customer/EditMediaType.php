<?php


namespace App\Form\Customer;


use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditMediaType extends AbstractType
{

    public function __construct()
    {
        $this->__tagRepo = null;
        $this->__mediaRepo = [];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->__tagRepo = $options['tagRepo'];
        $this->__mediaRepo = $options['mediaRepo'];

        $builder->add('name', TextType::class, [

                ])

                ->add('diffusionStart', DateType::class,[
                    'widget' => 'single_text',
                    'input_format' => 'd-m-Y',
                    'label' => 'Du',
                    'choice_translation_domain' => true,
                ])

                ->add('diffusionEnd', DateType::class,[
                    'widget' => 'single_text',
                    'input_format' => 'd-m-Y',
                    'label' => 'Au',
                    'choice_translation_domain' => true,
                ])

                ->add('tags', EntityType::class, [
                    'class' => Tag::class,
                    'choice_label' =>  'name',
                    'choices' => $this->__mediaRepo->getMediaAssociatedTags($options['data']),
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ])

                ->add('products', EntityType::class, [
                    'class' => Product::class,
                    'choice_label' =>  'name',
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Media::class,
       ])

            ->setRequired([
              'tagRepo',
              'mediaRepo'
          ])

        ;
    }

}