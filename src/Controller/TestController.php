<?php

namespace App\Controller;

use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Feature;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\Permission;
use App\Entity\Admin\TimeZone;
use App\Entity\Admin\User;
use App\Entity\Customer\Role;
use App\Entity\Customer\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/testroute", name="test")
     */
    public function index()
    {
        $em =  $this->getDoctrine()->getManager('Kfc');


        $site = new Site();
        $site->setName('Q021');
        $site->setAdress('22 rue de la cité');
        $site->setPostalCode('92300');
        $site->setCity('Paris');
        $site->setPhoneNumber('0142325622');
        $site->setDescription('une description');
        $site->setCountry(1);
        $site->setTimezone(1);
        $site->setCustomer(1);


        $em->persist($site);
        $em->flush();

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
