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

        return $this->render("media/media-image.html.twig", [

        ]);

    }


    /**
     * Return an json response which will contain files authorised extensions for upload
     *
     * @Route(path="/get/files/authorized/extensions", name="media::getFilesAuthorizedExtensions", methods={"GET"})
     */
    public function getFilesAuthorizedExtensions(): JsonResponse
    {
        return new JsonResponse( $this->getParameter('uploadAuthorizedMimeTypes') );

        /*return new JsonResponse( [
            'images' => [
                'jpeg', 'png', 'zip'
            ],
            'videos' => [

            ]
        ] );*/

    }


    /**
     * @Route(path="/upload/media", name="media::uploadMedia", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function uploadMedia(Request $request): Response
    {

        //move_uploaded_file($_FILES['file']['tmp_name'], $this->getParameter('uploadDirectory') . $this->getUser()->getCustomers() . $_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $this->getParameter('uploadDirectory') . $_FILES['file']['name']);

        return new Response("a");
        //dd($request->files);

    }


    /**
     * Return true (0) if file already exist in upload directory else false (1)
     *
     * @Route(path="/file/is/uploaded", name="media::fileIsAlreadyUploaded", methods={"POST"})
     */
    public function fileIsAlreadyUploaded(Request $request): Response
    {

        $response = 1; // false

        if(file_exists($this->getParameter('uploadDirectory') . $request->request->get('file')))
            $response = 0; // true

        return new Response($response);
    }


}