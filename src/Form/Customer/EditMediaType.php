<?php


namespace App\Form\Customer;


use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use App\Repository\Customer\MediaRepository;
use App\Repository\Customer\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use \Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditMediaType extends AbstractType
{

    private ?TagRepository $__tagRepo;

    private ?MediaRepository $__mediaRepo;

    public function __construct()
    {
        $this->__tagRepo = null;
        $this->__mediaRepo = null;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->__tagRepo = $options['tagRepo'];
        $this->__mediaRepo = $options['mediaRepo'];

        if(null === $this->__tagRepo)
            throw new \Exception("Missing TagRepository !");

        if(null === $this->__mediaRepo)
            throw new \Exception("Missing MediaRepository !");

        $builder->add('name', TextType::class, [

                ])

                ->add('diffusionStart', DateTimeType::class,[
                    'widget' => 'single_text',
                    'input_format' => 'd-m-Y H:i',
                    'label' => 'Début',
                    'choice_translation_domain' => true,
                ])

                ->add('diffusionEnd', DateTimeType::class,[
                    'widget' => 'single_text',
                    'input_format' => 'd-m-Y H:i',
                    'label' => 'Fin',
                    'choice_translation_domain' => true,
                ])

                ->add('tags', EntityType::class, [
                    'class' => Tag::class,
                    'choice_label' =>  'name',
                    //'choices' => $this->__mediaRepo->getMediaAssociatedTags($options['data']),
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ])

                ->add('products', EntityType::class, [
                    'class' => Product::class,
                    'choice_label' =>  'name',
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
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