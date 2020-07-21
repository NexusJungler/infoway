<?php

namespace App\Form\Customer;

use App\Object\Customer\ProductEditor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductEditorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // dd($options);
        $builder
            ->add('product', ProductType::class, [

                ])
            /*
            ->add('prices', MainPricesListType::class, [

            ])
            */
            ->add('prices', CollectionType::class, [
                'entry_type' => MainPriceType::class,
                'label' => false,
                'required' => false,
                'entry_options' => array('label' => false)
            ])

            ->add('checkoutMappings', CollectionType::class, [
                'entry_type' => CheckoutProductType::class,
                'label' => false,
                'required' => false,
                'entry_options' => array('label' => false)
            ])

            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductEditor::class,
        ]);
    }
}
