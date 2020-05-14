<?php


namespace App\Form;

use App\Entity\Admin\Allergen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllergenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Nom',
                'label_attr' => array('class' => 'label-custome')])

            ->add('description', TextareaType::class, [
                'required'   => true,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Description',
                'label_attr' => array('class' => 'label-custome')])
            ->add('pictogram', TextType::class, [
                'required'   => false,
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Pictogramme',
                'label_attr' => array('class' => 'label-custome')])


            ->add('valider', SubmitType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Allergen::class,
        ]);
    }

}