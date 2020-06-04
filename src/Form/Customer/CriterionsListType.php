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
            ->add('name')
            ->add('description')
            ->add('basicCriterionUsed', ChoiceType::class, [
                'label' => 'Définir un critère de base ?',
                'choices' => [
                    'Oui' => 1 ,
                ],
                'expanded' => true,
                // 'multiple'  => true,
                
            ])
            ->add('basicCriterion', CriterionInListType::class,[
                'required' => false,
                'label' => 'Critère de base'
            ])
            ->add('multiple',ChoiceType::class,  [
                'choices'  => [
                    'Multiple' => true,
                    'Unique' => false,
                ],
            ])
            ->add('criterions', CollectionType::class, array(
                'entry_type' => CriterionInListType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,                
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