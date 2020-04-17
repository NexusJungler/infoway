<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Customer\Media;
use App\Entity\Customer\Synchro;
use App\Repository\Admin\CustomerRepository;
use App\Service\ArraySearchRecursiveService;
use App\Service\FfmpegSchedule;
use App\Service\SessionManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;

class MediaController extends AbstractController
{

    /**
     * @Route(path="/mediatheque/{media}", name="media::showMediatheque", methods={"GET"},
     * requirements={"media": "[a-z_]+"})
     */
    public function showMediatheque(Request $request, string $media)
    {

        if($media === "video" or $media === "video_synchro" or $media === "video_thematic")
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
     *     requirements={"file_type": "[a-z_]+"})
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

    }


    /**
     * @Route(path="/upload/media", name="media::uploadMedia", methods={"POST"})
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface $cronLogger
     * @return Response
     * @throws \Exception
     */
    public function uploadMedia(Request $request, CustomerRepository $customerRepository, ParameterBagInterface $parameterBag, LoggerInterface $cronLogger): Response
    {

        if($request->request->get('media_type') === "video_synchro")
            $type = "synchro";

        elseif($request->request->get('media_type') === "video_thematic")
            $type = "thematics";

        else
            $type = "medias";

        $options = ['medias' => 'diff', 'thematics' => 'them', 'synchros' => 'sync'];

        $mediaType = $options[$type];

        $file = $_FILES['file'];

        $customer = $customerRepository->findOneByName('kfc'); // dynamic session variable (will change each time user select customer in select)

        $root = 'C:/laragon/www/infowaydev/node_file_system/';
        $path = $root . $customer->getName() . '/' . $mediaType . '/' . $file['name'];

        // debug
        // comment this at the end
        move_uploaded_file($file['tmp_name'], $path);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        $filetype = strstr($mimeType, '/', true);

        $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $parameterBag, $cronLogger);
        $ffmpegSchedule->pushTask($customer, $file['name'], $filetype, $mediaType);

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

        if(file_exists($this->getParameter('upload_dir') . '/' . $request->request->get('file')))
            $response = 0; // true

        return new Response($response);
    }


}