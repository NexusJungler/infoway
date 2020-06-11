<?php

namespace App\Form\Customer;

use App\Entity\Customer\Date;
use App\Entity\Customer\ExpectedChange;
use App\Entity\Customer\ExpectedChangesList;
use App\Entity\Customer\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpectedChangesListType extends AbstractType
{
    private $currentObject;
    private bool $_allowExpectedChanges = true;
    private bool $_allowCurrentObjectChoice = true;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->_allowCurrentObjectChoice = $options['allowCurrentObjectChoice'];
        $this->_allowExpectedChanges = $options['allowExpectedChanges'];

        if($this->_allowCurrentObjectChoice) {
            $this->currentObject = $options['data']->getCurrentObject();
            $nameSpaceExploded = explode('\\',get_class( $this->currentObject ));
            $className = end($nameSpaceExploded);
            $formClass = 'App\Form\Customer\\' . $className .'Type';

            $builder->add('currentObject', 'App\Form\Customer\\'.$className.'Type', [
                'label' => false
            ]);
        }
        if($this->_allowExpectedChanges) {
            $builder->add('expectedChanges', CollectionType::class, [
                'entry_type' => ExpectedChangeType::class,
                'entry_options' => [
                    'entityToChange' => new $formClass()
                ],
                'label' => false
            ]);
        }

        $builder
            ->add('expectedDates', EntityType::class, [
                'class' => Date::class,
                'label' => false,
                'multiple' => true,
                'expanded' => true
            ]);
            /*
            ->add('expectedDates', CollectionType::class, [
                'entry_type' => DateType::class,
            ]);
            */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpectedChangesList::class,
            'allowExpectedChanges' => true,
            'allowCurrentObjectChoice' => true
        ]);

        $resolver->setRequired([
            'allowExpectedChanges',
            'allowCurrentObjectChoice'
        ]);

    }
}

