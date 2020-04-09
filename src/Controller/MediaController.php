<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;


class MediaController extends AbstractController
{

    /**
     * @Route(path="/mediatheque", name="media::showMediatheque", methods={"GET"})
     */
    public function showMediatheque(Request $request)
    {

        return $this->render("image/media-image.html.twig", [

        ]);

    }


    /**
     * @Route(path="/get/files/authorized/extensions", name="media::getFilesAuthorizedExtensions", methods={"GET"})
     */
    public function getFilesAuthorizedExtensions(): JsonResponse
    {
        return new JsonResponse([
            'images' => [
                'jpeg', 'png', 'zip'
            ],
            'videos' => [

            ]
        ]);
    }


    /**
     * @Route(path="/upload/media", name="media::uploadMedia", methods={"POST"})
     * @param Request $request
     */
    public function uploadMedia(Request $request)
    {
        /*move_uploaded_file($_FILES['file']['tmp_name'], $this->getParameter('logoDirectory') . $_FILES['file']['name']);
        die;*/
        return new Response("a");
        //dd($request->files);

    }


}