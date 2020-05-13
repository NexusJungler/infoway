<?php

namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\DisplayMould;
use App\Entity\Customer\DisplaySpace;
use App\Entity\Customer\Tag;
use App\Repository\Customer\DisplayMouldRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayMouldType extends AbstractType
{
    private bool $allowPlaylistCreation = true ;
    private bool $allowDisplaySpaceCHoice = true ;
    private bool $allowModelChoice = false ;


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dd($this->displayMouldRepo);

        $this->allowDisplaySpaceCHoice = $options['allowDisplaySpaceChoice'] ;
        $this->allowPlaylistCreation = $options['allowPlaylistCreation'] ;
        $this->allowModelChoice = $options['allowModelChoice'] ;

        if( $this->allowDisplaySpaceCHoice ) {
            $builder->add('displaySpace', EntityType::class, [
                'class' => DisplaySpace::class,
                'choice_label' => 'name',
            ]) ;
        }

        $builder
            ->add('name') ;
//            ->add('startAt')
//            ->add('endAt') ;

        if( $this->allowPlaylistCreation ) {
            $builder->add('playlists');
        }

        $builder
            ->add('screensNumber')
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
                'class' => DisplayMould::class,
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
            'data_class' => DisplayMould::class,
            'allowPlaylistCreation' => true,
            'allowDisplaySpaceChoice'    => true,
            'allowModelChoice' => false
        ]);
        $resolver->setRequired([
            'allowPlaylistCreation' ,
            'allowDisplaySpaceChoice',
            'allowModelChoice'
        ]);
        $resolver->setAllowedTypes('allowPlaylistCreation','bool') ;
        $resolver->setAllowedTypes('allowDisplaySpaceChoice','bool') ;
        $resolver->setAllowedTypes('allowModelChoice','bool') ;
    }
}
