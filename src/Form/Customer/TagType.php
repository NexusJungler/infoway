<?php

namespace App\Form\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Repository\Customer\SiteRepository;
use App\Service\SessionManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{

    private User $_user ;
    private Customer $_customer ;
    private bool $_allowSiteChoice ;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->_user = $options[ 'user' ] ;
        $this->_customer = $options[ 'customer' ] ;
        $this->_allowSiteChoice = $options[ 'allowSiteChoice' ] ;

        $builder
            ->add('color', ColorType::class,[
                'attr' => [
                    'class' => 'tags-color',
                    'value' => ''
                ],  
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'tags-input',
                    'placeholder' => "PROMO",
                    
                ],
                // 'choice_label' => [
                //     'class' => 'test'
                // ],
            ])
           
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'tags-desc',
                    'placeholder' => "Produits promotionnel du 21 mai"
                ],
            ]);
        if ($this->_allowSiteChoice){
            $builder
                ->add('sites', EntityType::class, [
                    'class' => Site::class,
                    'choice_label' => 'name',
                    'query_builder' => function(SiteRepository $siteRepository ){
                        $siteRepository->getSitesByUserAndCustomer( $this->_user, $this->_customer ) ;
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'allowSiteChoice' => true,
        ]);
        $resolver->setRequired([
            'allowSiteChoice',
            'customer',
            'user'
        ]);
    }
}