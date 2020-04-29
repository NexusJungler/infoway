<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Customer\Media;
use App\Entity\Customer\Synchro;
use App\Repository\Admin\CustomerRepository;
use App\Repository\Customer\MediaRepository;
use App\Service\ArraySearchRecursiveService;
use App\Service\FfmpegSchedule;
use App\Service\SessionManager;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
     * @Route(path="/upload/media", name="media::uploadMedia", methods={"POST"})
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface $cronLogger
     * @return Response
     * @throws Exception
     */
    public function uploadMedia(Request $request, CustomerRepository $customerRepository, MediaRepository $mediaRepository, ParameterBagInterface $parameterBag, LoggerInterface $cronLogger): Response
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

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        $splash = explode('/', $mimeType);
        $real_file_extension = $splash[1];

        if(!in_array($real_file_extension, $this->getParameter("authorizedExtensions")))
            return new Response("512 Bad Extension", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if($mediaRepository->findOneByName( pathinfo($file['name'])['filename'] ) )
            return new Response("515 Duplicate File", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if(preg_match("/(\w)*\.(\w)*/", pathinfo($file['name'])['filename'] ))
            return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if($file['name'] === "" or $file['name'] === null)
            return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        $customer = $customerRepository->findOneByName('Kfc'); // dynamic session variable (will change each time user select customer in select)

        $root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $customer->getName() . '/' . $mediaType . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);

        // debug
        // comment this at the end
        copy($path, $root . $customer->getName() . '/' . $type . '/' . $file['name']);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        $filetype = strstr($mimeType, '/', true);

        if(!isset($file['diffusion-start-date']) OR empty($file['diffusion-start-date']))
            // now
            $diffusionStartDate = new DateTime();

        else
            // create date with user choice
            $diffusionStartDate = new DateTime($file['diffusion-start-date']);

        if(!isset($file['diffusion-end-date']) OR empty($file['diffusion-end-date']))
        {
            // now
            $diffusionEndDate = new DateTime();
            // add 30 year
            $diffusionEndDate->modify('+30 year');
        }
        else
            // create date with user choice
            $diffusionEndDate = new DateTime($file['diffusion-end-date']);

        $fileName = pathinfo($file['name'])['filename'] . '.' . pathinfo($file['name'])['extension'];

        $fileInfo = [
            'fileName' => $fileName,
            'customer' => $customer,
            'fileType' => $filetype,
            'mediaType' => $mediaType,
            'diffusionStart' => $diffusionStartDate,
            'diffusionEnd' => $diffusionEndDate,
            'containIncruste' => ( $file['add-price-incruste'] === 'yes' ) ? true : false,
        ];

        // register Ffmpeg task
        // a CRON will be encode media after
        $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $parameterBag, $cronLogger);
        $ffmpegSchedule->pushTask($fileInfo);

        return new Response("200 OK", Response::HTTP_OK);
        //dd($request->files);

    }


    /**
     * Return true (0) if file already exist in db else false (1)
     *
     * @Route(path="/file/is/uploaded", name="media::fileIsAlreadyUploaded", methods={"POST"})
     * @param Request $request
     * @param MediaRepository $mediaRepository
     * @return Response
     */
    public function fileIsAlreadyUploaded(Request $request, MediaRepository $mediaRepository): Response
    {
        return new Response( (!$mediaRepository->findOneByName($request->request->get('file'))) ? 1 : 0 );
    }

    // bug upload à corriger : parfois n'arrive pas à upload en high

    /**
     * @Route(path="/edit/media", name="media::editMedia", methods={"POST"})
     * @param Request $request
     * @param MediaRepository $mediaRepository
     * @return Response
     * @throws Exception
     */
    public function editMedia(Request $request, MediaRepository $mediaRepository, CustomerRepository $customerRepository, ParameterBagInterface $parameterBag, LoggerInterface $cronLogger)
    {

        $customer = $customerRepository->findOneByName('Kfc'); // dynamic session variable (will change each time user select customer in select)

        foreach ($request->request->get('files') as $file)
        {

            if($mediaRepository->findOneByName($file['name']))
                return new Response("515 Duplicate File", Response::HTTP_INTERNAL_SERVER_ERROR);

            else if(preg_match("/(\w)*\.(\w)*/", $file['name']))
                return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

            else if($file['name'] === "" or $file['name'] === null)
                return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

            else if(strlen($file['name']) < 5)
                return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

            else if(!$mediaRepository->findOneByName($file['old-name']))
                return new Response("404 File Not Found", Response::HTTP_INTERNAL_SERVER_ERROR);

            else
            {

                if($request->request->get('media_type') === "video_synchro")
                    $type = "synchro";

                elseif($request->request->get('media_type') === "video_thematic")
                    $type = "thematics";

                else
                    $type = "medias";

                $options = ['medias' => 'diff', 'thematics' => 'them', 'synchros' => 'sync'];

                $mediaType = $options[$type];

                $fileName = $file['name'] . '.' . $file['extension'];

                $root = $this->getParameter('project_dir') . '/../node_file_system/';
                $path = $root . $customer->getName() . '/' . $mediaType . '/' . $fileName;

                // if user change file name
                // rename uploaded file
                if($file['name'] !== $file['old-name'])
                {
                    rename($root . $customer->getName() . '/' . $mediaType . '/' . $file['old-name'] .'.' . $file['extension'], $path);

                    // debug
                    // comment this at the end
                    copy($path, $root . $customer->getName() . '/' . $type . '/' . $fileName);
                }

                // @TODO: update media
                // find media (with name ? id ?)
                // if not exist => media is not encoded
                // in this case, insert ?

                $media = $mediaRepository->findOneByName($file['old-name']);


                $mediaInfo = [

                ];


            }

        }

        return new Response("200 OK", Response::HTTP_OK);
    }

}