<?php


namespace App\Form\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\CriterionsList;
use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use App\Object\Customer\SitesList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddMediaType extends AbstractType
{
    private ArrayCollection $mediasToDisplay ;
    private ArrayCollection $tagsList ;
    private ArrayCollection $criterionsList  ;
    private ArrayCollection $availablesTimeSlots ;
    private array $allowedMediasTypes ;
    private int $screensQty ;

    public function __construct(){
        $this->mediasToDisplay = new ArrayCollection() ;
        $this->allowedMediasTypes = [] ;
        $this->tagsList = new ArrayCollection() ;
        $this->criterionsList = new ArrayCollection() ;
        $this->availablesTimeSlots = new ArrayCollection() ;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->mediasToDisplay =  $options[ 'mediasToDisplay' ] ;
        $this->allowedMediasTypes = $options[ 'allowedMediasTypes' ] ;
        $this->screensQty = $options['screensQty'] ;
        $this->availablesTimeSlots = $options['availablesTimeSlots'] ;

        $this->mediasToDisplay = $this->mediasToDisplay->filter( fn( Media $media) => $media ) ;

        $this->generateTagslist( $this->mediasToDisplay );
        $this->generateCriterionsList( $this->mediasToDisplay );



        $builder
            ->add('allowedMediasTypes',ChoiceType::class, [
                'choices' => $this->allowedMediasTypes ,
                'choice_label' => function( string $mediaType ){
                return $mediaType ;
                },
                'label' => false,
                'multiple' => false,
                'expanded' => true
            ])
            ->add('medias', ChoiceType::class, [
                'choices' => $this->mediasToDisplay ,
                'choice_label' => function(Media $media) {
                    return $media->getName();
                },
                'choice_value' => function (Media $media) {
                    return $media->getId() ;
                },
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('criterions', ChoiceType::class, [
                'choices' => $this->criterionsList ,
                'choice_label' => function(Criterion $criterion) {
                    return $criterion->getName();
                },
                'choice_value' => function (Criterion $criterion) {
                    return $criterion->getId() ;
                },
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('tags', ChoiceType::class, [
                'choices' => $this->tagsList ,
                'choice_label' => function(Tag $tag) {
                    return $tag->getName();
                },
                'choice_value' => function (Tag $tag) {
                    return $tag->getId() ;
                },
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('search', TextType::class,[
                'label' => false ,
                'data' => 'Rechercher un élément...'
            ])
            ->add('programming', AdvancedProgrammingType::class, [
                'label' => 'Programmation Avancée',
                'minStartDate' => $this->generateMinStartDate( $this->mediasToDisplay ),
                'maxEndDate' => $this->generateMaxEndDate( $this->mediasToDisplay ),
                'screensQty' => $this->screensQty,
                'availablesTimeSlots' => $this->availablesTimeSlots
            ])

        ;
    }

    private function generateMinStartDate( ArrayCollection $medias ){
        $allDiffusionStartDates = $medias->filter( fn( Media $media ) => $media->getDiffusionStart() instanceof \DateTime )->map( fn( Media $media ) => $media->getDiffusionStart() ) ;
        return  $allDiffusionStartDates->count() > 0 ? min( $allDiffusionStartDates->toArray() ) : new \DateTime('NOW');
    }
    private function generateMaxEndDate( ArrayCollection $medias ){
        $allDiffusionEndDates = $medias->filter( fn( Media $media ) => $media->getDiffusionEnd() instanceof \DateTime )->map( fn( Media $media ) => $media->getDiffusionEnd() ) ;
        return  $allDiffusionEndDates->count() > 0 ? max( $allDiffusionEndDates->toArray() ) : new \DateTime('NOW');
    }

    private function generateTagslist( $mediasList ){
        foreach( $mediasList as $media ){
            foreach( $media->getTags() as $tag ){
                if( !$this->tagsList->contains( $tag ) ) $this->tagsList[] = $tag ;
            }
        }
    }

    private function generateCriterionsList( $mediasList ){
        foreach( $mediasList as $media ){
            foreach( $media->getProducts() as $product ){
                foreach( $product->getCriterions() as $criterion ){
                    if( !$this->criterionsList->contains( $criterion ) ) $this->criterionsList[] = $criterion ;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $mediaList = $view->children['medias']->vars['choices'] ;
        foreach( $view['medias'] as $index => $media ){
            $media->vars['object'] = $mediaList[ $index ]->data ;
        }

    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mediasToDisplay' => new ArrayCollection(),
            'availablesTimeSlots' => new ArrayCollection(),
            'allowedMediasTypes' => [] ,
            'data_class' => null,
        ]);
        $resolver->setRequired([
            'availablesTimeSlots',
            'mediasToDisplay',
            'allowedMediasTypes',
            'screensQty'
        ]);
    }
}