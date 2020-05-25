<?php

namespace App\Form\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Admin\UserSites;
use App\Entity\Customer\Site;
use App\Entity\Customer\Product;
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
    private bool $_allowProductsChoice ;
    private $siteRepo ;

    public function __construct(SiteRepository $siteRepo ){
        $this->siteRepo = $siteRepo ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->_user = $options[ 'user' ] ;
        $this->_customer = $options[ 'customer' ] ;
        $this->_allowSiteChoice = $options[ 'allowSiteChoice' ] ;
        $this->_allowProductsChoice = $options[ 'allowProductsChoice' ] ;
        
        $builder
            ->add('color', ColorType::class,[
                'attr' => [
                    'class' => 'tags-color'
                ],  
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'tags-input tags-name',
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
                    'choices' =>  $this->siteRepo->getSitesByUserAndCustomer( $this->_user, $this->_customer ) ,
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false
                ]);
        }
        if ($this ->_allowProductsChoice){
            $builder
                ->add('products', EntityType::class, [
                    'class' => Product::class,
                    'choice_label' => 'name',
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
            'allowProductsChoice' => true,
        ]);
        $resolver->setRequired([
            'allowSiteChoice',
            'allowProductsChoice',
            'customer',
            'user'
        ]);
    }
}