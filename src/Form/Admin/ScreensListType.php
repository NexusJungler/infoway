<?php

namespace App\Form\Admin;

use App\Entity\Admin\Customer;
use App\Entity\Admin\Screen;
use App\Entity\Admin\ScreensList;
use App\Repository\Admin\ScreenRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreensListType extends AbstractType
{

    private $screensListDefaultOptions ;
    private bool $showOnlyAvailablesScreens = false;

    public function __construct()
    {
        $this->screensListDefaultOptions = [
            'class' => Screen::class ,
            'choice_label' => 'serialNumber',
            'multiple' => true,
            'expanded' => true
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if( $options[ 'showOnlyAvailablesScreens'] ) {
            $this->screensListDefaultOptions['query_builder'] = function( ScreenRepository $screenRepo ){
                return $screenRepo->createQueryBuilder('s')->andWhere('s.available = :available')->setParameter('available' , true ) ;
            } ;
        }
        $builder
            ->add('screensList', EntityType::class,
                $this->screensListDefaultOptions
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScreensList::class,
            'showOnlyAvailablesScreens' => false
        ]);

        $resolver->setRequired([
            'showOnlyAvailablesScreens'
        ]);

    }
}
