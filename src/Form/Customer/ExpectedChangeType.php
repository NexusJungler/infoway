<?php

namespace App\Form\Customer;

use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpectedChangeType extends AbstractType
{
    private AbstractType $importedForm;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $reflectionEntityToChange = new \ReflectionClass($options['entityToChange']);
        $this->importedForm = $options['entityToChange'];
        $classname = get_class($options['entityToChange']);

        $builder
            ->add('entityObject', $classname, [
                'label' => false
            ])
            /*
            ->add('expectedAt', EntityType::class, [
                'class' => Date::class,
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpectedChange::class,
        ]);

        $resolver->setRequired([
            'entityToChange'
        ]);

    }
}
