<?php


namespace App\Form;


use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\TimeZone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CustomerCreationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', TextType::class, [

                ])
                ->add('address', TextType::class, [

                ])
                ->add('phoneNumber', TextType::class, [

                ])
                ->add('postalCode', TextType::class, [

                ])
                ->add('country', EntityType::class, [
                    'class' => Country::class,
                    'choice_label' => 'name'
                ])
                ->add('city', TextType::class, [

                ])
                ->add('timezone', EntityType::class, [
                    'class' => TimeZone::class,
                    'choice_label' => 'name'
                ])
                ->add('description', TextareaType::class, [

                ])

        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }

}