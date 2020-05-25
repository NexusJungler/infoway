<?php


namespace App\Form\Customer;

use App\Entity\Customer\CriterionsList;
use App\Entity\Customer\Tag;
use App\Object\Customer\Action\TagsAction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsActionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('tags', EntityType::class ,
                [
                    'class' => Tag::class ,
                    'choice_label' => 'id' ,
                    'multiple' => true,
                    'expanded' => true,
                ])
            ->add('edit', SubmitType::class,
                [
                    'label' => 'Modifier'
                ])
            ->add('delete', SubmitType::class,
                [
                    'label' => 'Supprimer'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TagsAction::class,
        ]);
    }
}