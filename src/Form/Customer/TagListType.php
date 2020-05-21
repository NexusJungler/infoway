<?php

namespace App\Form\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Admin\TagsList;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Repository\Customer\SiteRepository;
use App\Service\SessionManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagListType extends AbstractType
{
    private User $_user ;
    private Customer $_customer ;



    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->_user = $options[ 'user' ] ;
        $this->_customer = $options[ 'customer' ] ;
        
        $builder
            ->add('tags', CollectionType::class,[
                // each entry in the array will be an "email" field
                'entry_type' => TagType::class,
                'entry_options'  => [
                    'customer' => $this->_customer ,
                    'user' => $this->_user,
                    'allowSiteChoice' => false
                ]
            ])
            ->add('sites', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'query_builder' => function(SiteRepository $siteRepository ){
                    $siteRepository->getSitesByUserAndCustomer( $this->_user, $this->_customer ) ;
                },
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TagsList::class,
        ]);
        $resolver->setRequired([
            'user' ,
            'customer'
        ]);
    }
}