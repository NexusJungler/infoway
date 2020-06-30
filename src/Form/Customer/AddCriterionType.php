<?php


namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\CriterionsList;
use App\Entity\Customer\Product;
use App\Object\Customer\SitesList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCriterionType extends AbstractType
{
private ArrayCollection $criterionsToDisplay ;

    public function __construct(){
        $this->criterionsToDisplay = new ArrayCollection() ;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->criterionsToDisplay =  $options['criterionsToDisplay'] ;
        $this->criterionsToDisplay = $this->criterionsToDisplay->filter( fn( Criterion $criterion) => $criterion ) ;

//        $this->listsInfosArray = [];
//        foreach($this->criterionsToDisplay as $criterionToDisplay){
//            //if( !isset($this->listsInfosArray[ $criterionToDisplay->getList()->getId() ]))$this->listsInfosArray[ $criterionToDisplay->getList()->getId() ]=[];
//            $this->listsInfosArray[ $criterionToDisplay->getList()->getId() ] = $this->listsInfosArray[ $criterionToDisplay->getList()->getId() ]  ??  $criterionToDisplay->getList() ;
//        }
//        $options['listsInfos'] = $this->listsInfosArray ;

        $builder
            ->add('criterions', ChoiceType::class, [
                'choices' => $this->criterionsToDisplay ,
                'group_by' => function( Criterion $choice, $key, $value ){
                    return $choice->getList()->getName();
                },
                'choice_label' => function(Criterion $criterion) {
                    return $criterion->getName();
                },
//                'data' => $options['listsInfos'],
                'choice_value' => function (Criterion $criterion) {
                    return $criterion->getId() ;
                },
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'criterionsToDisplay' => new ArrayCollection(),
            'data_class' => null,
        ]);
        $resolver->setRequired([
            'criterionsToDisplay',
        ]);
    }
}