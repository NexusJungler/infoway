<?php

namespace App\Form\Customer;

use App\Entity\Customer\ScreenPlaylistEntry;
use App\Form\DataTransformer\MediaToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreenPlaylistEntryType extends AbstractType
{
    private $transformer;

    public function __construct(MediaToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('positionInPlaylist')
            ->add('media',NumberType::class)
            ->get('media')
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScreenPlaylistEntry::class,
        ]);
    }
}
