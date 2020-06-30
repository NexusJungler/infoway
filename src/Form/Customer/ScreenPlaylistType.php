<?php

namespace App\Form\Customer;

use App\Entity\Customer\Media;
use App\Entity\Customer\MediaType;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Form\DataTransformer\MediaToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreenPlaylistType extends AbstractType
{

    private $transformer;

    public function __construct(MediaToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('screenPosition',NumberType::class,[
                'attr' => ['class' => 'playlist__position'],
                'label' => false
            ])
            ->add('entries', CollectionType::class,[
                'entry_type' => ScreenPlaylistEntryType::class,
                'attr' => ['class' => 'playlist__playlist_entries'],
                'entry_options' => ['label' => false],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScreenPlaylist::class,
        ]);
    }
}
