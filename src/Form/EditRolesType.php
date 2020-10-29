<?php


namespace App\Form;


use App\Entity\Customer\RoleList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRolesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('roles', CollectionType::class, [
            'label' => false,
            'required' => false,
            'entry_type' => RoleType::class,
            //'allow_add' => true,
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => RoleList::class,
       ]);
    }

}