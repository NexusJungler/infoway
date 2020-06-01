<?php


namespace App\Form\Customer;

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

class AddProductType extends AbstractType
{
    private ArrayCollection $productsToDisplay ;

    public function __construct(){
        $this->productsToDisplay = new ArrayCollection() ;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->productsToDisplay =  $options['productsToDisplay'] ;
        $this->productsToDisplay = $this->productsToDisplay->filter( fn( Product $product) => $product ) ;

        $builder
            ->add('products', ChoiceType::class, [
                'choices' => $this->productsToDisplay ,
                'choice_label' => function(Product $product) {
                    return $product->getName();
                },
                'choice_value' => function (Product $product) {
                    return $product->getId() ;
                },
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'productsToDisplay' => new ArrayCollection(),
            'data_class' => null,
        ]);
        $resolver->setRequired([
            'productsToDisplay'
        ]);
    }
}