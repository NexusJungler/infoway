<?php

namespace App\Form\Customer;

use App\Entity\Customer\Media;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Form\DataTransformer\MediaToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaInPlaylistType extends AbstractType
{
    private $transformer;

    public function __construct(MediaToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id',NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
      //      'data_class' => Media::class,
        ]);
    }
}
