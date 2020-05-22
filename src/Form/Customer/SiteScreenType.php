<?php

namespace App\Form\Customer;

use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\SiteScreen;
use App\Entity\Admin\Screen;
use App\Form\DataTransformer\MouldToLocalProgrammingTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteScreenType extends AbstractType
{

    private $modelTransformer ;
    private $screenIdToScreenTransformer ;

    /**
     * SiteScreenType constructor.
     * @param $modelTransformer
     */
    public function __construct(MouldToLocalProgrammingTransformer $modelTransformer)
    {
        $this->modelTransformer = $modelTransformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('screen', EntityType::class,
                [
                    'class' => Screen::class ,
                    'choice_label' => 'serialNumber'
                ])
//            ->add('site')
            ->add('localProgramming', LocalProgrammingType::class) ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteScreen::class,
        ]);
    }
}
