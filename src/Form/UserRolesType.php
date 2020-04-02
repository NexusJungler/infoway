<?php
namespace App\Form;

use App\Entity\Admin\Customer;
use App\Entity\Admin\User;
use App\Entity\Admin\UserRoles;
use App\Form\CustomerType;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type ;
class UserRolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//    $builder->add(
////        'user',UserType::class,[
////            'data_class' => User::class,
////        ]
////        );
    $builder->add(
        'roleId',IntegerType::class
        );
//    $builder->add(
//        'customer',CustomerType::class,[
//        'data_class' => Customer::class
//    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}