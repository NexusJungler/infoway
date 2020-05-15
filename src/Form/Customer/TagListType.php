<?php


namespace App\Form\Customer;

use App\Entity\Customer\CriterionsList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriterionsListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ->add('description')
            ->add('criterions', CollectionType::class, array(
                'entry_type' => CriterionInListType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ));

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CriterionsList::class,
        ]);
    }
}