<?php


namespace App\Form\Customer;

use App\Entity\Customer\CriterionsList;
use App\Entity\Customer\Site;
use App\Entity\Customer\TimeSlot;
use App\Object\Customer\SitesList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType ;

class AdvancedProgrammingType extends AbstractType
{
    private ArrayCollection $availablesTimeSlots ;
    private int $screensQty ;
    private $days = [ 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi','Samedi', 'Dimanche' ];
    private \DateTime $minStartDate ;
    private \DateTime $maxEndDate ;

    public function __construct(){
        $this->availablesTimeSlots = new ArrayCollection() ;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->availablesTimeSlots =  $options['availablesTimeSlots'] ;
        $this->availablesTimeSlots = $this->availablesTimeSlots->filter( function ( TimeSlot $timeSlot ){
            return $timeSlot ;
        } ) ;
        $this->minStartDate = $options['minStartDate'] ;
        $this->maxEndDate = $options['maxEndDate'] ;
        $this->screensQty =  $options['screensQty'] ;

        $builder
            ->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choices' => $this->availablesTimeSlots ,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
                'label'   => 'Sur les créneaux: '
            ])
            ->add('days', ChoiceType::class, [
                'choices' => array_flip( $this->days ),
                'multiple' => true,
                'expanded' => true,
                'label'    => 'Semaine: '
            ])
            ->add('screen', ChoiceType::class, [
                'choices' => $this->buildScreenChoiceArray( $this->screensQty ),
                'expanded' => true,
                'multiple' => true,
                'label' => 'Sur les écrans: '
            ])
            ->add('startDate', DateType::class, [
                'data' => $this->minStartDate,
                'widget' => 'single_text',
                'label' => 'DU: '
            ])
            ->add('endDate', DateType::class,[
                'data' => $this->maxEndDate,
                'widget' => 'single_text',
                'label' => 'AU: '
            ])
        ;
    }

    public function buildScreenChoiceArray( int $screensQty ){
        $screenChoiceArray = [] ;
        for($i = 1; $i <= $screensQty; $i++){
            $screenChoiceArray[] = $i;
        }
        return array_flip( $screenChoiceArray ) ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'availablesTimeSlots' => new ArrayCollection(),
            'minStartDate' => new \DateTime('NOW'),
            'maxEndDate'   => new \DateTime('NOW'),
            'screensQty' => 1,
            'data_class' => null,
        ]);
        $resolver->setRequired([
            'availablesTimeSlots',
            'minStartDate',
            'maxEndDate',
            'screensQty'
        ]);
    }
}