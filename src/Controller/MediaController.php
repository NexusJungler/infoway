<?php


namespace App\Controller;


use App\Repository\Admin\CustomerRepository;
use App\Service\ArraySearchRecursiveService;
use App\Service\FfmpegSchedule;
use App\Service\SessionManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;


class MediaController extends AbstractController
{

    /**
     * @Route(path="/mediatheque/{media}", name="media::showMediatheque", methods={"GET"},
     * requirements={"media": "[a-z_]+"})
     */
    public function showMediatheque(Request $request, string $media)
    {

        if($media === "video" or $media === "video_sync" or $media === "video_thematics")
            $media_displayed = "video";

        else
            $media_displayed = $media;


        return $this->render("media/media-image.html.twig", [
            'media_displayed' => $media_displayed // will be used by js for get authorized extensions for upload
        ]);

    }


    /**
     * Return an json response which will contain files authorised extensions for upload
     *
     * @Route(path="/get/{file_type}/authorized/extensions", name="media::getFilesAuthorized", methods={"GET"},
     *     requirements={"file_type": "[a-z]+"})
     * @param string $file_type
     * @return JsonResponse
     */
    public function getFilesAuthorized(string $file_type): JsonResponse
    {

        $fileAuthorizedInfos = [];

        if(array_key_exists($file_type, $this->getParameter('uploadAuthorizedMimeTypes')))
            //$fileExtensions['mime_types'] = $this->getParameter('uploadAuthorizedMimeTypes')[$file_type];
            $fileAuthorizedInfos = $this->getParameter('uploadAuthorizedMimeTypes')[$file_type];

        dump($this->getParameter('uploadAuthorizedMimeTypes'), array_key_exists($file_type, $this->getParameter('uploadAuthorizedMimeTypes')), $fileAuthorizedInfos);

        return new JsonResponse( $fileAuthorizedInfos );

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
     * @throws \Exception
     */
    public function uploadMedia(Request $request, EntityManagerInterface $entityManager, CustomerRepository $customerRepository, ParameterBagInterface $parameterBag): Response
    {

        if($request->request->get('media_type') === "video_sync")
            $type = "synchros";

        elseif($request->request->get('media_type') === "video_thematics")
            $type = "thematics";

        else
            $type = "medias";

        $file = $_FILES['file'];

        $customer = $customerRepository->findOneByName('kfc'); // dynamic session variable (will change each time user select customer in select)

        $options = ['medias' => 'diff', 'thematics' => 'them', 'synchros' => 'sync'];

        $mediaType = $options[$type];

        // D:/main/data_CUSTOMER/PLAYER INFOWAY WEB/IMAGES/PRODUITS FIXES/PLEIN ECRAN/HIGH
        // D:/main/data_CUSTOMER/PLAYER INFOWAY WEB/VIDÉOS/HIGH

        $root = 'C:\main\\data_';
        $path = $root . strtoupper($customer->getName()) . '\\' . $mediaType . '\\' . $file['name'];

        // insertion du fichier dans ffmpeg_tasks


        // si video, encodage puis ajout dans la mediatheque
        // sinon ajout dans la mediathe

        //move_uploaded_file($_FILES['file']['tmp_name'], $this->getParameter('uploadDirectory') . $this->getUser()->getCustomers() . $_FILES['file']['name']);
        //move_uploaded_file($_FILES['file']['tmp_name'], $this->getParameter('uploadDirectory') . $_FILES['file']['name']);

        //$path = $this->getParameter('uploadDirectory');

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        $filetype = strstr($mimeType, '/', true);

        $encoding = new FfmpegSchedule($entityManager, $parameterBag);
        //$customer_id = $encoding->getCustomerId($_POST['customer']); // à implanter dans la classe courante (private)
        $encoding->pushTask($customer, $file['name'], $filetype, $mediaType);
        $encoding->runTasks();

        return new Response("ok");
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