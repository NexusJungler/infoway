<?php

namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\DisplaySetting;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\DisplaySpace;
use App\Entity\Customer\Tag;
use App\Repository\Customer\MediaRepository;
use App\Repository\Customer\ProgrammingMouldRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammingMouldType extends AbstractType
{
    private bool $allowPlaylistCreation = true ;
    private bool $allowDisplaySettingChoice = true ;
    private bool $allowModelChoice = false ;
    private bool $allowAllowedMediaTypeChoice = false ;
    private MediaRepository $mediaRepository ;


    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository ;
       // dd($test);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dd($this->ProgrammingMouldRepo);

        $this->allowDisplaySettingChoice = $options['allowDisplaySettingChoice'] ;
        $this->allowPlaylistCreation = $options['allowPlaylistCreation'] ;
        $this->allowModelChoice = $options['allowModelChoice'] ;
        $this->allowAllowedMediaTypeChoice = $options['allowAllowedMediaTypeChoice'] ;

        if( $this->allowDisplaySettingChoice ) {
            $builder->add('displaySetting', EntityType::class, [
                'class' => DisplaySetting::class,
                'choice_label' => 'name',
            ]) ;
        }

        if( $this->allowAllowedMediaTypeChoice ){

            $displayablesMedias = $this->mediaRepository->getAllDisplayableMediasTypes();

            $displayablesMediasValuesToDisplay = array_map(
                function( $mediaType){
                    return ucfirst($mediaType).'s';
                },
                $displayablesMedias
            );

            $displayablesMediasChoices = array_combine( $displayablesMediasValuesToDisplay , $displayablesMedias )  ;

            $builder->add('allowedMediasTypes', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => $displayablesMediasChoices
            ]) ;
        }
        $builder
            ->add('name') ;
//            ->add('startAt')
//            ->add('endAt') ;

        if( $this->allowPlaylistCreation ) {
            $builder->add('displays', CollectionType::class , [
                'entry_type' => DisplayType::class,
            ]);
        }

        $builder
            ->add('criterions', EntityType::class , [
                'class' => Criterion::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'em' => 'kfc',
                'by_reference' => false
            ])
            ->add('tags', EntityType::class , [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'em' => 'kfc',
                'by_reference' => false
            ])
        ;

        if( $this->allowModelChoice ) {
            $builder->add('model', EntityType::class , [
                'class' => ProgrammingMould::class,
                'choice_label' => 'name',
                'em' => 'kfc',
                'by_reference' => false,
                'expanded' => true
            ]);
        }

        $builder->add('timeslots', CollectionType::class, [
            'entry_type' => TimeSlotType::class
        ] ) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProgrammingMould::class,
            'allowPlaylistCreation' => true,
            'allowDisplaySettingChoice'    => true,
            'allowModelChoice' => false,
            'allowAllowedMediaTypeChoice' => true
        ]);
        $resolver->setRequired([
            'allowPlaylistCreation' ,
            'allowDisplaySettingChoice',
            'allowModelChoice',
            'allowAllowedMediaTypeChoice'
        ]);
        $resolver->setAllowedTypes('allowPlaylistCreation','bool') ;
        $resolver->setAllowedTypes('allowDisplaySettingChoice','bool') ;
        $resolver->setAllowedTypes('allowModelChoice','bool') ;
        $resolver->setAllowedTypes('allowAllowedMediaTypeChoice','bool') ;
    }
}
