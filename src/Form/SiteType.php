<?php

namespace App\Form;

use App\Entity\Admin\Country;
use App\Entity\Admin\TimeZone;
use App\Entity\Admin\User;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\Devise;
use App\Entity\Customer\PricesGroup;
use App\Entity\Customer\Screen;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Form\Customer\NightProgrammingType;
use App\Form\Customer\ScreenType;
use App\Form\Customer\SiteScreenType;
use App\Form\DataTransformer\SitesScreenToScreenTransformer;
use App\Repository\Admin\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteType extends AbstractType
{
    private $userRepository;

private $siteScreenToScreenTransformer ;
    public function __construct(UserRepository $userRepository, SitesScreenToScreenTransformer $siteScreenToScreenTransformer)
    {
        $this->userRepository = $userRepository;
        $this->siteScreenToScreenTransformer = $siteScreenToScreenTransformer ;
    }

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
                    'choice_label' =>  function (User $user) {
                        return $user->getFirstName() .' ' . $user->getLastName();
                    },
                    'choices' =>  $this->userRepository->getUsersWithRoleBellowUserByCustomer($customer, $creator) ,
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
            ->add('pricesGroup',EntityType::class ,
                [
                    // looks for choices from this entity
                    'class' => PricesGroup::class,
                    'choice_label' => 'name',
                    'by_reference' => false
                ])
//            ->add('screens', EntityType::class)
            ->add('screens', CollectionType::class ,
                [
                    'entry_type' => SiteScreenType::class,
                ])
         //   ->add('nightProgrammingActivated', CheckboxType::class,)
         //   ->add('nightProgramming',NightProgrammingType::class)
//            ->add('tags')
        ;

        $builder->get('screens')
            ->addModelTransformer( $this->siteScreenToScreenTransformer );
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
