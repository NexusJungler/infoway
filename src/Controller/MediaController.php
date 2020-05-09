<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Admin\FfmpegTasks;
use App\Entity\Customer\Category;
use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\MediasList;
use App\Entity\Customer\Product;
use App\Entity\Customer\Synchro;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Video;
use App\Form\MediasListType;
use App\Repository\Admin\CustomerRepository;
use App\Repository\Admin\FfmpegTasksRepository;
use App\Repository\Customer\MediaRepository;
use App\Service\ArraySearchRecursiveService;
use App\Service\FfmpegSchedule;
use App\Service\SessionManager;
use App\Service\UploadCron;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

class MediaController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/mediatheque/{media}", name="media::showMediatheque", methods={"GET"},
     * requirements={"media": "[a-z_]+"})
     */
    public function showMediatheque(Request $request, string $media, SessionManager $sessionManager)
    {

        if($media === "video" or $media === "video_synchro" or $media === "video_thematic")
            $media_displayed = "video";

        else
            $media_displayed = $media;

        $products = $this->getDoctrine()->getManager( strtolower( $sessionManager->get('userCurrentCustomer') ) )->getRepository(Product::class)->findAll();
        $categories = $this->getDoctrine()->getManager( strtolower( $sessionManager->get('userCurrentCustomer') ) )->getRepository(Category::class)->findAll();
        $tags = $this->getDoctrine()->getManager( strtolower( $sessionManager->get('userCurrentCustomer') ) )->getRepository(Tag::class)->findAll();

        // upload is not accessible in 'template' and 'incrustations' tab
        $uploadIsAuthorizedOnPage = ($media_displayed !== 'template' AND $media_displayed !== 'incruste');

        $mediaList = new MediasList();
        $form = $this->createForm(MediasListType::class, $mediaList, [
            'action' => $this->generateUrl('media::editMedia'),
            'attr' => [
                'id' => 'medias_list_form'
            ]
        ]);

        /*$form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            dd($form);
        }*/

        return $this->render("media/media-image.html.twig", [
            'media_displayed' => $media_displayed, // will be used by js for get authorized extensions for upload
            'uploadIsAuthorizedOnPage' => $uploadIsAuthorizedOnPage,
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags,
            'form' => $form->createView(),
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
    public function uploadMedia(Request $request, CustomerRepository $customerRepository, FfmpegTasksRepository $ffmpegTasksRepository, SessionManager $sessionManager, ParameterBagInterface $parameterBag): Response
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
        $filetype = strstr($mimeType, '/', true);

        $customerName = strtolower( $sessionManager->get('userCurrentCustomer') );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $mediaRepository = $manager->getRepository(Media::class);

        if(!in_array($real_file_extension, $this->getParameter("authorizedExtensions")))
            return new Response("512 Bad Extension", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if($mediaRepository->findOneByName( pathinfo($file['name'])['filename'] ) )
            return new Response("515 Duplicate File", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if(preg_match("/(\w)*\.(\w)*/", pathinfo($file['name'])['filename'] ))
            return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if($file['name'] === "" or $file['name'] === null)
            return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if(strlen(pathinfo($file['name'])['filename']) < 5)
            return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        $root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $customerName . '/' . $mediaType . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);

        // debug
        // comment this at the end
        copy($path, $root . $customerName . '/' . $type . '/' . $file['name']);

        // if is image, insert immediately
        if($splash[0] === 'image')
        {

            $taskInfo = [
                'fileName' => $file['name'],
                'customerName' => $customerName,
                'mediaType' => $mediaType,
                'uploadDate' => new DateTime(),
                'extension' => $real_file_extension,
            ];

            // don't duplicate code !!
            // reuse this class
            $cron = new UploadCron($taskInfo, $this->getDoctrine(), $parameterBag);
            if( $cron->getErrors() !== [] )
                throw new Exception( sprintf("Internal Error : 1 or multiple errors during insert new image ! Errors : '%s'", implode(' ; ', $cron->getErrors())) );

            $id = $mediaRepository->findOneByName( str_replace('.'.$real_file_extension, '', $file['name']) )->getId();

        }

        else
        {

            $fileName = pathinfo($file['name'])['filename'] . '.' . pathinfo($file['name'])['extension'];

            $fileInfo = [
                'fileName' => $fileName,
                //'customer' => $sessionManager->get('userCurrentCustomer'),

                // quand on stocke l'objet dans la session, on obtient une erreur lorsque l'on fait $customer->addUploadTask() dans FfmpegSchedule
                // et lors du dump, on obtient un tableau vide avec le $customer->getUploadTasks()
                'customer' => $customerRepository->findOneByName( $customerName ),
                'fileType' => $filetype,
                'type' => $mediaType,
                'extension' => $real_file_extension,
            ];

            // register Ffmpeg task
            // a CRON will do task after
            $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $parameterBag, $this->serializer);
            $ffmpegSchedule->pushTask($fileInfo);

            $id = $ffmpegTasksRepository->findOneByFilename( $fileName )->getId();

        }

        return new Response($id, Response::HTTP_OK);
        //dd($request->files);

    }


    /**
     * Return true (0) if file already exist in db else false (1)
     *
     * @Route(path="/file/is/uploaded", name="media::fileIsAlreadyUploaded", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function fileIsAlreadyUploaded(Request $request, FfmpegTasksRepository $tasksRepository, SessionManager $sessionManager): Response
    {

        $fileNameWithExtension = $request->request->get('file');
        $mediaRepository = $this->getDoctrine()->getManager( strtolower( $sessionManager->get('userCurrentCustomer') ) )->getRepository(Media::class);

        $explode = explode('.', $request->request->get('file'));
        $fileNameWithoutExtension = $explode[0];

        // if file is in Media or if file is in FfmpegTasks but not already
        if($mediaRepository->findOneByName($fileNameWithoutExtension) OR $tasksRepository->findOneBy(['filename' => $fileNameWithExtension, 'finished' => null]))
            $output = 0;

        else
            $output = 1;

        return new Response( $output );
    }

    /**
     * Check uploaded file Ffmpeg task status
     *
     * @Route(path="/get/uploaded/file/process/status", name="media::getMediaFfmpegTaskStatus", methods={"POST"})
     */
    public function getMediaFfmpegTaskStatus(Request $request, FfmpegTasksRepository $tasksRepository, CustomerRepository $customerRepository, SessionManager $sessionManager)
    {

        $customer = $customerRepository->findOneByName($sessionManager->get('userCurrentCustomer'));
        $task = $tasksRepository->findOneBy([ 'filename' => $request->request->get('file'), 'customer' => $customer ]);
        if(!$task)
            return new Response("404 Task not found", Response::HTTP_INTERNAL_SERVER_ERROR);

        else if ($task->getStarted() === null)
            return new Response("Not Started");

        else if ($task->getFinished() === null)
            return new Response("Not Finished");

        else if ($task->getFinished() !== null AND $task->getErrors() !== null)
            return new Response("Finished with errors");

        else
            return new Response("Finished");

    }


    /**
     * @Route(path="/get/file/miniature/path", name="media::getMediaMiniaturePath", methods={"POST"})
     */
    public function getMediaMiniaturePath(Request $request, SessionManager $sessionManager)
    {

        $manager = $this->getDoctrine()->getManager(strtolower( $sessionManager->get('userCurrentCustomer') ));
        $mediaRepository = $manager->getRepository(Media::class);
        $media = $mediaRepository->findOneByName($request->request->get('file'));

        if(!$media)
            return new Response("404 File Not Found", Response::HTTP_INTERNAL_SERVER_ERROR);

        else
        {

            if( $media instanceof Video )
                $mediaType = "video";

            else
                $mediaType = "IMAGES";

            $path = $this->getParameter('project_dir') . "/../main/data_" . strtolower( $sessionManager->get('userCurrentCustomer') ) . "/PLAYER INFOWAY WEB/medias/" . $mediaType . "/low/" . $media->getId() . "." . $media->getExtension();
            //$path = "http://127.0.0.1:8000/../main/data_" . strtolower( $sessionManager->get('userCurrentCustomer') ) . "/PLAYER INFOWAY WEB/medias/" . $mediaType . "/low/" . $media->getId() . "." . $media->getExtension();

            return new Response($path);

        }

    }


    /**
     * @Route(path="/edit/media", name="media::editMedia", methods={"POST"})
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param FfmpegTasksRepository $ffmpegTasksRepository
     * @param SessionManager $sessionManager
     * @return Response
     * @throws Exception
     */
    public function editMedia(Request $request, CustomerRepository $customerRepository, FfmpegTasksRepository $ffmpegTasksRepository, SessionManager $sessionManager)
    {

        $customer = $customerRepository->findOneByName( strtolower( $sessionManager->get('userCurrentCustomer') ) ); // dynamic session variable (will change each time user select customer in select)
        $manager = $this->getDoctrine()->getManager( strtolower( $sessionManager->get('userCurrentCustomer') ) );


        //dd($request->request, $customer);

        $error = [  ];

        foreach ($request->request->get('medias_list')['medias'] as $index => $mediaInfos)
        {

            if(preg_match("/(\w)*\.(\w)*/", $mediaInfos['name']))
            {
                // return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '516 Invalid Filename', 'subject' => $index ];
                break;
            }

            else if($mediaInfos['name'] === "" or $mediaInfos['name'] === null)
            {
                // return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '517 Empty Filename', 'subject' => $index ];
                break;
            }

            else if(strlen($mediaInfos['name']) < 5)
            {
                // return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '518 Too short Filename', 'subject' => $index ];
                break;
            }

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

                $fileName = $mediaInfos['name'] . '.' . $mediaInfos['extension'];

                $root = $this->getParameter('project_dir') . '/../node_file_system/';
                $path = $root . $customer->getName() . '/' . $mediaType . '/' . $fileName;

                // check date (@see: https://www.php.net/manual/en/function.checkdate.php)
                // if date is not return new Response("519 Invalid diffusion date", Response::HTTP_INTERNAL_SERVER_ERROR);
                if(!checkdate($mediaInfos['diffusionStart']['month'] ,$mediaInfos['diffusionStart']['day'] ,$mediaInfos['diffusionStart']['year']))
                {
                    $error = [ 'text' => '519.1 Invalid diffusion start date', 'subject' => $index ];
                }

                if(!checkdate($mediaInfos['diffusionEnd']['month'] ,$mediaInfos['diffusionEnd']['day'] ,$mediaInfos['diffusionEnd']['year']))
                {
                    $error = [ 'text' => '519.2 Invalid diffusion end date', 'subject' => $index ];
                }

                $diffusionStartDate = new DateTime( $mediaInfos['diffusionStart']['year'] . '-' . $mediaInfos['diffusionStart']['month'] . '-' . $mediaInfos['diffusionStart']['day'] );
                $diffusionEndDate = new DateTime( $mediaInfos['diffusionEnd']['year'] . '-' . $mediaInfos['diffusionEnd']['month'] . '-' . $mediaInfos['diffusionEnd']['day'] );

                if($diffusionEndDate < $diffusionStartDate)
                {
                    $error = [ 'text' => '519 Invalid diffusion date', 'subject' => $index ];
                }

                $task = $ffmpegTasksRepository->findOneBy(['filename' => $mediaInfos['oldName'].".".$mediaInfos['extension']]);

                // si aucune task n'est trouvé et que ce n'est pas une image qui a été edité (donc c'est une video)
                if(!$task && $mediaInfos['mediaType'] !== 'image')
                    throw new Exception(sprintf("No Task found with filename '%s' !", $mediaInfos['oldName'].".".$mediaInfos['extension']));

                $media = $manager->getRepository(Media::class)->findOneByName( $mediaInfos['oldName'] );

                if(!$media)
                {
                    $media = new Video();

                    $media->setName( $mediaInfos['name'] );
                }
                else
                {
                    $media->setName( ($media->getName() !== $mediaInfos['name']) ? $mediaInfos['name'] : $media->getName() );
                }

                $media->setDiffusionStart($diffusionStartDate)
                      ->setDiffusionEnd($diffusionEndDate);

                // if user change file name
                // rename uploaded file
                if($mediaInfos['name'] !== $mediaInfos['oldName'] && file_exists($root . $customer->getName() . '/' . $mediaType . '/' . $mediaInfos['oldName'] .'.' . $mediaInfos['extension']))
                {

                    rename($root . $customer->getName() . '/' . $mediaType . '/' . $mediaInfos['oldName'] .'.' . $mediaInfos['extension'], $path);

                    // debug
                    // comment this at the end
                    copy($path, $root . $customer->getName() . '/' . $type . '/' . $fileName);

                }


                if(array_key_exists('tags', $mediaInfos))
                {
                    foreach ($mediaInfos['tags'] as $k => $tagId)
                    {
                        $tag = $manager->getRepository(Tag::class)->find($tagId);
                        if(!$tag)
                            throw new Exception(sprintf("No Tag found with id : '%d'", $tagId));

                        $media->addTag($tag);
                    }
                }

                if(array_key_exists('products', $mediaInfos))
                {
                    foreach ($mediaInfos['products'] as $k => $productId)
                    {
                        $product = $manager->getRepository(Product::class)->find($productId);
                        if(!$product)
                            throw new Exception(sprintf("No Product found with id : '%d'", $productId));

                        $media->addProduct($product);
                    }
                }

                if(!$task)
                    $manager->flush();

                else
                {

                    $media = json_decode($this->serializer->serialize($media, 'json'), true);

                    $task->setFilename( $mediaInfos['name'] . '.' . $mediaInfos['extension'] )
                         ->setMedia($media);

                    $this->getDoctrine()->getManager('default')->flush();

                }

            }

        }

        return new JsonResponse( ($error === []) ? '200 OK' : $error , ($error === []) ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function updateTask(FfmpegTasks $task, Media $media)
    {

        $mediaName = $media->getName() . '.' . $media->getExtension();

        $media = json_decode($this->serializer->serialize($media, 'json'), true);

        $task->setFilename(( $task->getFilename() !== $mediaName ) ? $mediaName : $task->getFilename())
            ->setMedia($media);

        $this->getDoctrine()->getManager('default')->flush();

    }

}