<?php

namespace App\DataFixtures;

use App\Entity\Admin\Perimeter;
use App\Entity\Admin\User;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

     public static function getGroups(): array
     {
         return ['group1'];
     }

    public function load(ObjectManager $manager)
    {
        $user = new User();

       $perimeter = $manager->getRepository(Perimeter::class)->findOneById(1);
        $user
            ->setPerimeter($perimeter)
            ->setFirstName('John')
            ->setLastName('X')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'test'
            ))
            ->setPhoneNumber('0143256232')
            ->setActivated(0)
            ->setEmail('john@gmail.com');
        $manager->persist($user);
        $manager->flush();


        $user = new User();

        $user
            ->setPerimeter($perimeter)
            ->setFirstName('Toto')
            ->setLastName('toto')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'totoRtyu3$'
            ))
            ->setPhoneNumber('0143256232')
            ->setActivated(0)
            ->setEmail('cbaby@infoway.fr');
        $manager->persist($user);
        $manager->flush();


    }
}