<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CriterionController extends AbstractController
{
    /**
     * @Route("/criterion/create", name="criterion")
     */
    public function index()
    {
        return $this->render('criterion/index.html.twig', [
            'controller_name' => 'CriterionController',
        ]);
    }
}
