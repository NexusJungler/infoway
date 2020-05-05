<?php


namespace App\Form;

use App\Entity\Customer\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Customer\Tag;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
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
            ->add('color', TextType::class, [
                'attr' => [
                    'class' => 'input-custome'
                ],
                'label' => 'Nom',
                'label_attr' => array('class' => 'label-custome')])

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }

}

