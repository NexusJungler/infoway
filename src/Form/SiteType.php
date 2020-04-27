<?php

namespace App\Form;

use App\Entity\Admin\Country;
use App\Entity\Admin\TimeZone;
use App\Entity\Admin\User;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\Devise;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customer = $options['customer'] ;
        $creator =  $options['creator'] ;

        $builder
            ->add('name')
            ->add('adress')
            ->add('postalCode')
            ->add('city')
            ->add('phoneNumber')
            ->add('observations')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'by_reference' => false
            ])
            ->add('devise', EntityType::class, [
                'class' => Devise::class,
                'choice_label' => 'symbol',
            ])
            ->add('timezone', EntityType::class,[
                'class' => TimeZone::class,
                'choice_label' => 'name',
                'by_reference' => false
            ])
            ->add('users', EntityType::class ,
                [
                    // looks for choices from this entity
                    'class' => User::class,
                    'choice_label' => 'first_name',
                    'query_builder' => function( EntityRepository $userRepo ) use ( $customer, $creator ){
                        $userRepo->getUsersWithRoleBellowUserByCustomer($customer, $creator);
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ])
            ->add('criterions',EntityType::class ,
                [
                    // looks for choices from this entity
                    'class' => Criterion::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ])
            ->add('tags',EntityType::class ,
                [
                    // looks for choices from this entity
                    'class' => Tag::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ])
//            ->add('tags')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Site::class,
        ])
            ->setRequired([
                'customer',
                'creator'
            ])
        ;
    }
}
