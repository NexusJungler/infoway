<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{

    /**
     *
     * @Route("/", name="app")
     * @Route("/template", name="app::home")
     *
     * @param Request $request
     * @return Response
     */
    public function homePage(Request $request): Response
    {

//        if($this->getUser() === null)
//            return $this->redirectToRoute("user::login");

        $customer = [
            'ARES',
            'Q087',
            'AEAS',
            'Q087A2',
            'ARAS',
            'Q08'
        ];

        $location = (object) [
            'city' => 'Paris',
            'timezone' => 'Europe/Paris',
            'date_format' => 'd-m-Y',
            'clock_format' => 24
        ];

        //dump($location);

        return $this->render("home.html.twig", [
            'customer' => $customer,
            'location' => $location
        ]);
    }



}