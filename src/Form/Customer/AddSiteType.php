<?php


namespace App\Form\Customer;

use App\Entity\Customer\CriterionsList;
use App\Entity\Customer\Site;
use App\Object\Customer\SitesList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSiteType extends AbstractType
{
    private ArrayCollection $sitesToDisplay ;

    public function __construct(){
        $this->sitesToDisplay = new ArrayCollection() ;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->sitesToDisplay =  $options['sitesToDisplay'] ;
        $this->sitesToDisplay = $this->sitesToDisplay->filter( fn( Site $site) => $site ) ;


        $builder
            ->add('sites', ChoiceType::class, [
                'choices' => $this->sitesToDisplay ,
                'choice_label' => function(Site $site) {
                    return $site->getName();
                },
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'sitesToDisplay' => new ArrayCollection(),
            'data_class' => null,
        ]);
        $resolver->setRequired([
            'sitesToDisplay'
        ]);
    }
}