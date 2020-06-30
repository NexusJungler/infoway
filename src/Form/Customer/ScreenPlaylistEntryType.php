<?php

namespace App\Form\Customer;

use App\Entity\Customer\Media;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Form\DataTransformer\MediaToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreenPlaylistEntryType extends AbstractType
{
    private $transformer;
    private ?Media $mediaLoaded ;

    public function __construct(MediaToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->mediaLoaded = null;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('positionInPlaylist')
            ->add('media',TextType::class)


            ->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetDataAttachMedia']
            );


        $builder
            ->get('media')
            ->addModelTransformer( $this->transformer ) ;
        ;

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media'] = $this->mediaLoaded;

//        parent::buildView($view, $form, $options);
    }

    public function onPreSetDataAttachMedia(FormEvent $event)
    {
       $screenPlaylistEntry = $event->getData() ;
       if( $screenPlaylistEntry !== null ){
           if( $screenPlaylistEntry->getMedia() !== null ){ $this->mediaLoaded = $screenPlaylistEntry->getMedia() ; }
       }
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScreenPlaylistEntry::class,
        ]);
    }
}
