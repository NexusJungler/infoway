<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Admin\FfmpegTasks;
use App\Entity\Customer\Category;
use App\Entity\Customer\Incruste;
use App\Entity\Customer\Media;
use App\Entity\Customer\MediasList;
use App\Entity\Customer\Product;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Video;
use App\Form\MediasListType;
use App\Repository\Admin\CustomerRepository;
use App\Repository\Admin\FfmpegTasksRepository;
use App\Service\FfmpegSchedule;
use App\Service\MediasHandler;
use App\Service\SessionManager;
use App\Service\UploadCron;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, Session\SessionInterface};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use \Doctrine\Persistence\ObjectManager;


class MediaController extends AbstractController
{

    private SerializerInterface $serializer;

    private SessionManager $sessionManager;

    private ParameterBagInterface $parameterBag;

    public function __construct(SessionManager $sessionManager, ParameterBagInterface $parameterBag)
    {

        $circularReferenceHandlingContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoder =  new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $circularReferenceHandlingContext);
        $this->serializer = new Serializer( [ $normalizer, new DateTimeNormalizer() ] , [ $encoder ] );

        $this->sessionManager = $sessionManager;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route(path="/mediatheque/{mediasDisplayedType}/{page}", name="media::showMediatheque", methods={"GET", "POST"},
     * requirements={"mediasDisplayedType": "[a-z_]+", "page": "\d+"})
     * @throws Exception
     */
    public function showMediatheque(Request $request, string $mediasDisplayedType, int $page = 1)
    {

        $managerName = strtolower( $this->sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $managerName );

        $productRepo = $manager->getRepository(Product::class)->setEntityManager( $manager );
        $categoryRepo = $manager->getRepository(Category::class)->setEntityManager( $manager );
        $tagRepo = $manager->getRepository(Tag::class)->setEntityManager( $manager );
        $mediaRepo = $manager->getRepository(Media::class)->setEntityManager( $manager );
        $incrusteRepo = $manager->getRepository(Incruste::class)->setEntityManager( $manager );

        $products = $productRepo->findAll();
        $categories = $categoryRepo->findAll();
        $tags = $tagRepo->findAll();
        $productsCriterions = $productRepo->findProductsCriterions();
        $mediasWaitingForIncrustes = $mediaRepo->getMediasInWaitingListForIncrustes();
        $allArchivedMedias = $mediaRepo->getAllArchivedMedias();

        if($this->sessionManager->get('numberOfMediasDisplayedInMediatheque') === null)
            $this->sessionManager->set('numberOfMediasDisplayedInMediatheque', 15);

        list($mediasToDisplayed, $numberOfPages, $numberOfMediasAllowedToDisplayed) = $this->getMediasForMediatheque($manager, $request);

        //$numberOfPages = intval( ceil($mediasToDisplayed['size'] / $numberOfMediasDisplayedInMediatheque) );
        //$numberOfMediasAllowedToDisplayed = $mediasToDisplayed['numberOfMediasAllowedToDisplayed'];

        //dump($mediasToDisplayed, $mediasToDisplayed['numberOfMediasAllowedToDisplayed']);

        // boolean pour savoir si le bouton d'upload doit être afficher ou pas
        $uploadIsAuthorizedOnPage = ($mediasDisplayedType !== 'template' AND $mediasDisplayedType !== 'incruste');

        $mediaList = new MediasList();
        $form = $this->createForm(MediasListType::class, $mediaList, [
            'attr' => [
                'id' => 'medias_list_form'
            ]
        ]);

        // @TODO: remplacer le formulaire dans la popup d'upload par le form créer par le formbuilder et laisser symfony gérer la validation (assert)
        /*$form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {

        }*/

        if($request->isMethod('POST'))
        {
            return $this->saveMediaCharacteristic($request);
        }


        return $this->render("media/media-image.html.twig", [
            'media_displayed' => $mediasDisplayedType, // will be used by js for get authorized extensions for upload
            'uploadIsAuthorizedOnPage' => $uploadIsAuthorizedOnPage,
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags,
            'productsCriterions' => $productsCriterions,
            'form' => $form->createView(),
            'mediasWaitingForIncrustes' => $mediasWaitingForIncrustes,
            'mediasToDisplayed' => $mediasToDisplayed,
            'numberOfPages' => $numberOfPages,
            'numberOfMediasAllowedToDisplayed' => $numberOfMediasAllowedToDisplayed,
            'archivedMedias' => $allArchivedMedias,
        ]);

    }


    /**
     * @Route(path="/upload/media", name="media::uploadMedia", methods={"POST"})
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param SessionManager $sessionManager
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @throws Exception
     */
    public function uploadMedia(Request $request, CustomerRepository $customerRepository): Response
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
        $fileType = strstr($mimeType, '/', true);

        $mediasHandler = new MediasHandler($this->parameterBag);

        $customerName = strtolower( $this->sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        if($mediasHandler->fileIsCorrupt($file['tmp_name'], $fileType))
            return new Response("514 Corrupt File", Response::HTTP_INTERNAL_SERVER_ERROR);

        elseif(!in_array($real_file_extension, $this->getParameter("authorizedExtensions")))
            return new Response("512 Bad Extension", Response::HTTP_INTERNAL_SERVER_ERROR);

        elseif($mediaRepository->findOneByName( pathinfo($file['name'])['filename'] ) )
            return new Response("515 Duplicate File", Response::HTTP_INTERNAL_SERVER_ERROR);

        elseif(preg_match("/(\w)*\.(\w)*/", pathinfo($file['name'])['filename'] ))
            return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        elseif($file['name'] === "" or $file['name'] === null)
            return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);

        /*else if(strlen(pathinfo($file['name'])['filename']) < 5)
            return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);*/

        $root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $customerName . '/' . $mediaType . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);

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
                'mediaProducts' => [],
                'mediaTags' => [],
                'mediaContainIncruste' => false,
                'mimeType' => $mimeType,
                'isArchived' => false,
            ];

            // don't duplicate code !!
            // reuse this class
            $cron = new UploadCron($taskInfo, $this->getDoctrine(), $this->parameterBag);
            if( $cron->getErrors() !== [] )
            {

                $errors = implode(' ; ', $cron->getErrors());

                if($errors === "bad ratio")
                    return new Response("521 Bad ratio", Response::HTTP_INTERNAL_SERVER_ERROR);

                else
                    throw new Exception( sprintf("Internal Error : 1 or multiple errors during insert new image ! Errors : '%s'", implode(' ; ', $cron->getErrors())) );

            }

            $name = explode('.',$file['name'])[0];
            $media = $mediaRepository->findOneByName( $name );
            if(!$media)
                throw new Exception(sprintf("No media found with name : '%s'", $name));

            $miniaturePath = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . "/images/low/" . $media->getId() . ".png";

            if(!file_exists($miniaturePath))
                throw new Exception(sprintf("Miniature file is not found ! This path is correct ? : '%s'", $miniaturePath));

            $dpi = $mediasHandler->getImageDpi($miniaturePath);

            $highestFormat = $this->getMediaHigestFormat($media->getId(), "image");

            $response = [
                'id' => $media->getId(),
                'extension' => $media->getExtension(),
                'height' => $media->getHeight(),
                'width' => $media->getWidth(),
                'dpi' => $dpi,
                'type' => 'image',
                'customer' => $customerName,
                'highestFormat' => $highestFormat,
            ];

        }

        else
        {

            $fileName = pathinfo($file['name'])['filename'] . '.' . pathinfo($file['name'])['extension'];
            $customer = $customerRepository->findOneByName( $customerName );

            if($fileType === 'video')
                $media = new Video();

            // need new Entity (e.g : for powerpoint, word, ...)
            else
                throw new Exception(sprintf("Need new media type implementation for type '%s'", $fileType));

            $media->setName( str_replace( '.' . $real_file_extension, null, $fileName) )
                  ->setExtension($real_file_extension)
                  ->setMimeType($mimeType)
                  ->setContainIncruste(false)
                  ->setIsArchived(false)
                  ->setType($mediaType);

            $media = json_decode($this->serializer->serialize($media, 'json'), true);

            $fileInfo = [
                'fileName' => $fileName,
                //'customer' => $sessionManager->get('current_customer'),

                // quand on stocke l'objet dans la session, on obtient une erreur lorsque l'on fait $customer->addUploadTask() dans FfmpegSchedule
                // et lors du dump, on obtient un tableau vide avec le $customer->getUploadTasks()
                'customer' => $customer,
                'fileType' => $fileType,
                'type' => $mediaType,
                'extension' => $real_file_extension,
                'media' => $media,
                'mediaContainIncruste' => false,
                'isArchived' => false,
            ];

            list($width, $height, $codec) = $mediasHandler->getVideoDimensions($path);

            // register Ffmpeg task
            // a CRON will do task after
            $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $this->parameterBag);
            $id = $ffmpegSchedule->pushTask($fileInfo);

            $highestFormat = $this->getMediaHigestFormat($id, "video");

            $response = [
                'id' => $id,
                'extension' => $real_file_extension,
                'height' => $height,
                'width' => $width,
                'codec' => $codec ?? null,
                'type' => 'video',
                'customer' => $customerName,
                'mimeType' => $mimeType,
                'highestFormat' => $highestFormat,
            ];

        }

        // @TODO: if resolution 16/9, add color red on resolution in popup when user click save, confirm(error..., continue ?)

        return new JsonResponse($response, Response::HTTP_OK);

    }

    /**
     * @Route(path="/get/video/encoding/status", name="media::getMediaEncodingStatus", methods={"POST"})
     */
    public function getMediaEncodingStatus(Request $request, FfmpegTasksRepository $ffmpegTasksRepository)
    {

        $task = $ffmpegTasksRepository->find($request->request->get('id'));
        if(!$task)
            throw new Exception(sprintf("No Ffmpeg task found with id : '%d'", $request->request->get('id')));

        // finish with 0 errors
        if($task->getFinished() !== null AND $task->getErrors() === null)
        {

            $customerName = strtolower( $this->sessionManager->get('current_customer')->getName() );
            $manager = $this->getDoctrine()->getManager( $customerName );
            $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

            $media = $mediaRepository->findOneByName( $task->getMedia()['name'] );
            if(!$media)
                throw new Exception(sprintf("No Media found with name : '%s", $task->getMedia()['name']));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $path = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . "/videos/low/" . $media->getId() . "." . $media->getExtension();
            $mimeType = finfo_file($finfo, $path);

            $response = [
                'status' => 'Finished',
                'id' => $media->getId(),
                'extension' => $media->getExtension(),
                'fileName' => $task->getFilename(),
                'height' => $media->getHeight(),
                'width' => $media->getWidth(),
                'codec' => $media->getVideoCodec(),
                'type' => 'video',
                'customer' => $customerName,
                'mimeType' => $mimeType,
                'name' => $media->getName()
            ];

        }

        // finish with 1 or more errors
        elseif($task->getFinished() !== null AND $task->getErrors() !== null)
            $response = ['status' => 'Finished', 'type' => '520 Encode error', 'error' => $task->getErrors()];

        // not finish
        else
            $response = ['status' => 'Running'];

        return new JsonResponse($response , (array_key_exists('error', $response)) ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK);

    }


    /**
     * @Route(path="/remove/media", name="media::removeMedia", methods={"POST"})
     */
    public function removeMedia(Request $request)
    {

        $managerName = strtolower($this->sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        $id = $request->request->get('media');
        //dd($id);
        $media = $mediaRepository->find($id);

        if(!$media)
            throw new Exception(sprintf("No media found with id : '%s'", $id));

        /*
        // delete source
        $root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $managerName . '/' . $media->getType() . '/' . $media->getName() . '.' . $media->getExtension();
        unlink($path);*/

        if($media instanceof Video)
            $mediaType = 'video';

        else
            $mediaType = 'image';

        $sizes = ['low', 'medium', 'high', 'HD'];
        foreach ($sizes as $size) {
            $path = $this->getParameter('project_dir') .'/../main/data_' . $managerName . '/PLAYER INFOWAY WEB/medias/' . $mediaType . '/' .$size .'/' . $media->getId() . '.' . $media->getExtension();

            if(file_exists($path))
                unlink($path);
        }

        $manager->remove($media);
        $manager->flush();

        return new Response("200 OK");
    }


    /**
     * Return true (0) if file already exist in db else false (1)
     *
     * @Route(path="/file/is/uploaded", name="media::fileIsAlreadyUploaded", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function fileIsAlreadyUploaded(Request $request, FfmpegTasksRepository $tasksRepository): Response
    {

        $fileNameWithExtension = $request->request->get('file');
        $managerName = strtolower($this->sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        $explode = explode('.', $request->request->get('file'));
        $fileNameWithoutExtension = $explode[0];

        $task = $tasksRepository->findOneBy(['filename' => $fileNameWithExtension, 'finished' => null]);

        // if file is in Media or if file is in FfmpegTasks but not already
        if($mediaRepository->findOneByName($fileNameWithoutExtension) OR $task)
        {
            $output = 0;
        }

        else
            $output = 1;

        return new Response( $output );
    }


    /**
     * @Route(path="/get/more/medias/for/mediatheque", name="media::getMoreMediasForMediatheque", methods={"POST"})
     * @param Request $request
     */
    public function getMoreMediasForMediatheque(Request $request)
    {

        $managerName = strtolower( $this->sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $managerName );

        if($request->request->get('numberOfMediasToDisplay') !== null)
        {

            $number = intval($request->request->get('numberOfMediasToDisplay'));

            if(!is_int($number))
                throw new Exception(sprintf("'numberOfMediasDisplayedInMediatheque' Session varaible cannot be update with '%s' value beacause it's not int!", $number));

            ( $this->sessionManager->get('numberOfMediasDisplayedInMediatheque') !== null ) ?
                $this->sessionManager->replace('numberOfMediasDisplayedInMediatheque', $number) :
                $this->sessionManager->set('numberOfMediasDisplayedInMediatheque', $number);

        }

        list($mediasToDisplayed, $numberOfPages, $numberOfMediasAllowedToDisplayed) = $this->getMediasForMediatheque($manager, $request, true);

        $mediasToDisplayed['customer'] = strtolower($this->sessionManager->get('current_customer')->getName());

        return new JsonResponse([
            'mediasToDisplayed' => $mediasToDisplayed,
            'numberOfPages' => $numberOfPages,
            'numberOfMediasAllowedToDisplayed' => $numberOfMediasAllowedToDisplayed,
            'userMediasDisplayedChoice' => $this->sessionManager->get('numberOfMediasDisplayedInMediatheque'),
        ]);

    }


    private function saveMediaCharacteristic(Request $request)
    {

        $ffmpegTasksRepository = $this->getDoctrine()->getManager()->getRepository(FfmpegTasks::class);
        $customerRepository = $this->getDoctrine()->getManager()->getRepository(Customer::class);

        $customer = $customerRepository->find( $this->sessionManager->get('current_customer')->getId() ); // dynamic session variable (will change each time user select customer in select)
        $manager = $this->getDoctrine()->getManager( strtolower( $this->sessionManager->get('current_customer')->getName() ) );


        dd($request->request, $customer);

        $error = [  ];

        foreach ($request->request->get('medias_list')['medias'] as $index => $mediaInfos)
        {

            if(preg_match("/(\w)*\.(\w)*/", $mediaInfos['name']))
            {
                // return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '516 Invalid Filename', 'subject' => $index ];
                break;
            }

            elseif($mediaInfos['name'] === "" or $mediaInfos['name'] === null)
            {
                // return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '517 Empty Filename', 'subject' => $index ];
                break;
            }

            /*elseif(strlen($mediaInfos['name']) < 5)
            {
                // return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $error = [ 'text' => '518 Too short Filename', 'subject' => $index ];
                break;
            }*/

            else
            {

                $mediaInfos['mediaType'] = 'video';

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
                    break;
                }

                if(!checkdate($mediaInfos['diffusionEnd']['month'] ,$mediaInfos['diffusionEnd']['day'] ,$mediaInfos['diffusionEnd']['year']))
                {
                    $error = [ 'text' => '519.2 Invalid diffusion end date', 'subject' => $index ];
                    break;
                }

                $diffusionStartDate = new DateTime( $mediaInfos['diffusionStart']['year'] . '-' . $mediaInfos['diffusionStart']['month'] . '-' . $mediaInfos['diffusionStart']['day'] );
                $diffusionEndDate = new DateTime( $mediaInfos['diffusionEnd']['year'] . '-' . $mediaInfos['diffusionEnd']['month'] . '-' . $mediaInfos['diffusionEnd']['day'] );

                if($diffusionEndDate < $diffusionStartDate)
                {
                    $error = [ 'text' => '519 Invalid diffusion date', 'subject' => $index ];
                    break;
                }

                $media = $manager->getRepository(Media::class)->setEntityManager($manager)->find( $mediaInfos['id'] );

                $media->setName( $mediaInfos['name'] )
                      ->setContainIncruste( $mediaInfos['containIncruste'] )
                      ->setDiffusionStart($diffusionStartDate)
                      ->setDiffusionEnd($diffusionEndDate);

                // if user change file name
                // rename uploaded file
                if($media && $mediaInfos['name'] !== $media->getName() && file_exists($root . $customer->getName() . '/' . $mediaType . '/' . $media->getName() .'.' . $mediaInfos['extension']))
                {

                    rename($root . $customer->getName() . '/' . $mediaType . '/' . $media->getName() .'.' . $mediaInfos['extension'], $path);

                    // debug
                    // comment this at the end
                    copy($path, $root . $customer->getName() . '/' . $type . '/' . $fileName);

                }

                if(array_key_exists('tags', $mediaInfos))
                {
                    $media->getTags()->clear();
                    foreach ($mediaInfos['tags'] as $k => $tagId)
                    {
                        $tag = $manager->getRepository(Tag::class)->setEntityManager($manager)->find($tagId);
                        if(!$tag)
                            throw new Exception(sprintf("No Tag found with id : '%d'", $tagId));

                        $media->addTag($tag);
                    }
                }

                if(array_key_exists('products', $mediaInfos))
                {
                    $media->getProducts()->clear();
                    foreach ($mediaInfos['products'] as $k => $productId)
                    {
                        $product = $manager->getRepository(Product::class)->setEntityManager($manager)->find($productId);
                        if(!$product)
                            throw new Exception(sprintf("No Product found with id : '%d'", $productId));

                        $media->addProduct($product);
                    }
                }

                $manager->flush();

            }

        }

        return new JsonResponse( ($error === []) ? '200 OK' : $error , ($error === []) ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getMediaHigestFormat(int $id, string $mediaType)
    {

        $formats = [
            'HD', 'high', 'medium', 'low'
        ];

        $customerName = $this->sessionManager->get('current_customer')->getName();

        $root = $path = $this->getParameter('project_dir') . "/../main/data_" . $customerName . "/PLAYER INFOWAY WEB/medias";

        if($mediaType === 'image')
            $path = $root. "/image";

        else
            $path = $root. "/video";

        foreach ($formats as $format)
        {
            //dd($path . "/" .$format . "/" . $id . ( ($mediaType === "image") ? ".png" : ".mp4") );
            if(file_exists( $path . "/" .$format . "/" . $id . ( ($mediaType === "image") ? ".png" : ".mp4") ))
                return $format;
        }

        return  'low';
    }
    
    private function getMediasForMediatheque(ObjectManager &$manager, Request &$request, bool $serializeResults = false)
    {

        $mediaRepo = $manager->getRepository(Media::class)->setEntityManager( $manager );

        $numberOfMediasDisplayedInMediatheque = $this->sessionManager->get('numberOfMediasDisplayedInMediatheque');

        $mediasDisplayedType = $request->get('mediasDisplayedType');
        $page = intval($request->get('page'));

        if($mediasDisplayedType === "template")
        {
            die("TODO: get all templates");
        }

        elseif($mediasDisplayedType === "video_synchro")
        {
            die("TODO: get all video synchro");
        }

        elseif($mediasDisplayedType === "video_thematic")
        {
            die("TODO: get all video thematic");
        }

        elseif($mediasDisplayedType === "element_graphic")
        {
            die("TODO: get all element graphic");
        }

        elseif($mediasDisplayedType === "incruste")
        {
            die("TODO: get all incrustes");
        }

        else
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque($mediasDisplayedType, $page, $numberOfMediasDisplayedInMediatheque);

        $numberOfPages = $mediasToDisplayed['numberOfPages'];
        $numberOfMediasAllowedToDisplayed = $mediasToDisplayed['numberOfMediasAllowedToDisplayed'];
        unset($mediasToDisplayed['numberOfPages']);
        unset($mediasToDisplayed['numberOfMediasAllowedToDisplayed']);

        if($serializeResults)
        {
            $mediasToDisplayed = json_decode( $this->serializer->serialize($mediasToDisplayed, 'json'), true);

            foreach ($mediasToDisplayed['medias'] as &$mediaInfos)
            {

                foreach ($mediaInfos['media'] as $key => $value)
                {
                    if(is_array($value) AND array_key_exists('timezone', $value))
                    {
                        $date = new DateTime();
                        $date->setTimestamp($value['timestamp']);
                        $mediaInfos['media'][$key] = $date->format('Y-m-d H:i:s');
                    }

                }

            }

        }

        return [
            $mediasToDisplayed,
            $numberOfPages,
            $numberOfMediasAllowedToDisplayed,
        ];

    }

}