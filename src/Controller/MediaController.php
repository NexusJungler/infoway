<?php


namespace App\Controller;


use App\Entity\Admin\Customer;
use App\Entity\Admin\FfmpegTasks;
use App\Entity\Admin\ThematicTheme;
use App\Entity\Customer\Category;
use App\Entity\Customer\Criterion;
use App\Entity\Customer\Image;
use App\Entity\Customer\Incruste;
use App\Entity\Customer\Media;
use App\Entity\Customer\MediasList;
use App\Entity\Customer\Product;
use App\Entity\Customer\Synchro;
use App\Entity\Customer\SynchroElement;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Video;
use App\Form\Customer\EditMediaType;
use App\Form\MediasListType;
use App\Repository\Admin\CustomerRepository;
use App\Repository\Admin\FfmpegTasksRepository;
use App\Service\ArraySearchRecursiveService;
use App\Service\FfmpegSchedule;
use App\Service\MediasHandler;
use App\Service\SessionManager;
use App\Service\UploadedImageFormatsCreator;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, Session\SessionInterface};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    private SerializerInterface $__serializer;

    private SessionManager $__sessionManager;

    private ParameterBagInterface $__parameterBag;

    private MediasHandler $__mediasHandler;

    public function __construct(SessionManager $sessionManager, ParameterBagInterface $parameterBag)
    {

        $circularReferenceHandlingContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoder =  new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $circularReferenceHandlingContext);
        $this->__serializer = new Serializer( [ $normalizer, new DateTimeNormalizer() ] , [ $encoder ] );

        $this->__sessionManager = $sessionManager;
        $this->__parameterBag = $parameterBag;
        $this->__mediasHandler = new MediasHandler($this->__parameterBag);
    }

    /**
     * @Route(path="/mediatheque/{mediasDisplayedType}/{page}", name="media::showMediatheque", methods={"GET"},
     * requirements={"mediasDisplayedType": "[a-z_]+", "page": "\d+"})
     * @param Request $request
     * @param string $mediasDisplayedType
     * @param int $page
     * @return Response
     * @throws Exception
     */
    public function showMediatheque(Request $request, string $mediasDisplayedType, int $page = 1, SessionManager $sessionManager)
    {

        $managerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $managerName );

        $productRepo = $manager->getRepository(Product::class)->setEntityManager( $manager );
        $categoryRepo = $manager->getRepository(Category::class)->setEntityManager( $manager );
        $tagRepo = $manager->getRepository(Tag::class)->setEntityManager( $manager );
        $mediaRepo = $manager->getRepository(Media::class)->setEntityManager( $manager );
        $incrusteRepo = $manager->getRepository(Incruste::class)->setEntityManager( $manager );
        $criterionRepo = $manager->getRepository(Criterion::class)->setEntityManager( $manager );
        $synchroRepo = $manager->getRepository(Synchro::class)->setEntityManager( $manager );
        $videoThematicThemeRep = $this->getDoctrine()->getManager( 'default' )->getRepository(ThematicTheme::class);

        $products = $productRepo->findAll();
        $categories = $categoryRepo->findAll();
        $tags = $tagRepo->findAll();
        $criterions = $criterionRepo->findAll();
        $productsCriterions = $productRepo->findProductsAssociatedDatas('criterions');
        $productsTags = $productRepo->findProductsAssociatedDatas('tags');
        $mediasWaitingForIncrustation = $mediaRepo->getMediasInWaitingListForIncrustes();
        $allArchivedMedias = $mediaRepo->getAllArchivedMedias();

        if($mediasDisplayedType === "video_thematic")
        {
            //$videoThematicThemes = $videoThematicThemeRep->findAll();
            $videoThematicThemesPrototype = "<select name='__name__' id='__id__'>";

            $videoThematicThemesPrototype .= "<option value=''>Choisir un thème</option>";

            foreach ($videoThematicThemeRep->findAll() as $theme)
            {
                $videoThematicThemesPrototype .= "<option value='" . $theme->getId() . "'>" .$theme->getName() . "</option>";
            }

            $videoThematicThemesPrototype .= "</select>";
        }

        if($mediasDisplayedType === "video_synchro")
        {
            $allSynchrosNames = $synchroRepo->findAllNames();

            $this->__sessionManager->set('existed_synchro_names', $allSynchrosNames) ;
        }

        if($this->__sessionManager->get('mediatheque_medias_number') === null)
            $this->__sessionManager->set('mediatheque_medias_number', 15);

        list($mediasToDisplayed, $numberOfPages, $numberOfMediasAllowedToDisplayed) = $this->getMediasForMediatheque($manager, $request);

        if(empty($mediasToDisplayed))
            throw new NotFoundHttpException(sprintf("No media(s) found for this page !"));

        $allMediasNames = $mediaRepo->findAllNames();

        //dd($numberOfPages);

        // boolean pour savoir si le bouton d'upload doit être afficher ou pas
        $uploadIsAuthorizedOnPage = ($mediasDisplayedType !== 'template' AND $mediasDisplayedType !== 'incruste');

        /*$mediaList = new MediasList();
         //$mediaList->addMedia( $mediaRepo->find(47) ) ;

        $uploadMediaForm = $this->createForm(MediasListType::class, $mediaList, [
            'attr' => [
                'id' => 'medias_list_form'
            ]
        ]);*/

        // @TODO: remplacer le formulaire dans la popup d'upload par le form créer par le formbuilder et laisser symfony gérer la validation (assert)
        /*$form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {

        }*/

        /*if($request->isMethod('POST'))
            return $this->saveMediaCharacteristic($request);*/

        //dd($mediasToDisplayed);

        return $this->render("media/media-image.html.twig", [
            'media_displayed' => $mediasDisplayedType, // will be used by js for get authorized extensions for upload
            'uploadIsAuthorizedOnPage' => $uploadIsAuthorizedOnPage,
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags,
            'criterions' => $criterions,
            'productsCriterions' => $productsCriterions,
            'productsTags' => $productsTags,
            //'uploadMediaForm' => $uploadMediaForm->createView(),
            'mediasWaitingForIncrustation' => $mediasWaitingForIncrustation,
            'mediasToDisplayed' => $mediasToDisplayed,
            'numberOfPages' => $numberOfPages,
            'numberOfMediasAllowedToDisplayed' => $numberOfMediasAllowedToDisplayed,
            'archivedMedias' => $allArchivedMedias,
            'allMediasNames' => $allMediasNames,
            'allSynchrosNames' => isset($allSynchrosNames) ? $allSynchrosNames : [],
            //'videoThematicThemes' => isset($videoThematicThemes) ? $videoThematicThemes : []
            'videoThematicThemesPrototype' => isset($videoThematicThemesPrototype) ? $videoThematicThemesPrototype : "",
        ]);

    }

    /**
     * @Route(path="/edit/media/{id}", name="media::edit", methods={"GET", "POST"},
     * requirements={"id": "\d+"})
     */
    public function edit(Request $request, int $id)
    {

        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);

        $productRepo = $manager->getRepository(Product::class)->setEntityManager( $manager );
        $tagRepo = $manager->getRepository(Tag::class)->setEntityManager( $manager );
        $mediaRepo = $manager->getRepository(Media::class)->setEntityManager( $manager );
        $categoryRepo = $manager->getRepository(Category::class)->setEntityManager( $manager );


        $productsCriterions = $productRepo->findProductsAssociatedDatas('criterions');
        $productsTags = $productRepo->findProductsAssociatedDatas('tags');

        $media = $mediaRepo->find($id);
        if(!$media)
            throw new Exception(sprintf("No media can be found with this id : '%s'", $id));

        $mediaInfos = $mediaRepo->getMediaInfosForEdit($id);

        $medias = $mediaRepo->getAllMediasExcept([$media]);

        $form = $this->createForm(EditMediaType::class, $media, [
            'tagRepo' => $tagRepo,
            'mediaRepo' => $mediaRepo,
        ]);

        $currentCustomerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );

        $miniature_medium_exist = file_exists($this->__parameterBag->get('project_dir') . "/public/miniatures/" . $currentCustomerName . "/" .
                                              (($media instanceof Image) ? 'image': 'video') . "/medium/". $media->getId() . "." .
                                              ( ($media instanceof Image) ? 'png' : 'mp4' ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            dd($media);

        }

        $popupsFiltersContent = $this->getPopupFiltersContent();

        //dd($mediaInfos, $media, $form->createView());

        $characteristics = [
            'size' => $media->getWidth() . '*' . $media->getHeight() . ' px',
            'extension' => $media->getExtension(),
        ];

        if($media instanceof Video)
            $characteristics['codec'] = $media->getVideoCodec();

        else
            $characteristics['dpi'] = '72 dpi';

        return $this->render("media/edit_media.html.twig", [
            'products' => $popupsFiltersContent['products'],
            'tags' =>$popupsFiltersContent['tags'],
            'categories' => $popupsFiltersContent['categories'],
            'criterions' => $popupsFiltersContent['criterions'],
            'productsCriterions' =>$productsCriterions,
            'productsTags' =>$productsTags,
            'mediaInfos' =>$mediaInfos,
            'form' => $form->createView(),
            'media' => $media,
            'medias' => $medias,
            'file_type' => ($media instanceof Video) ? 'video': 'image',
            'media_characteristics' => $characteristics,
            'media_incrustations' => $mediaInfos['media_incrustations'],
            'media_criterions' => $mediaInfos['media_criterions'],
            'media_tags' => $mediaInfos['media_tags'],
            'media_allergens' => $mediaInfos['media_allergens'],
            'action' => 'edit',
            'sousTitle' => 'Modifier',
            'miniature_medium_exist' => $miniature_medium_exist,
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

        $type = $request->request->get('media_type');

        $options = ['medias' => 'diff', 'thematics' => 'them', 'synchros' => 'sync', 'element_graphic' => 'elmt'];

        if(!array_key_exists($type, $options))
            throw new Exception(sprintf("Array key not found ! '%s' is given (Allow : %s)", $type, implode(', ', array_keys($options))));

        $mediaType = $options[$type];

        $file = $_FILES['file'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        $splash = explode('/', $mimeType);
        $real_file_extension = $splash[1];
        $fileType = strstr($mimeType, '/', true);

        $customerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        if($this->__mediasHandler->fileIsCorrupt($file['tmp_name'], $fileType))
            return new JsonResponse([ 'error' => '514 Corrupt File' ]);

        elseif(!in_array($real_file_extension, $this->getParameter("authorizedExtensions")))
            return new JsonResponse([ 'error' => '512 Bad Extension' ]);

        elseif($mediaRepository->findOneByName( pathinfo($file['name'])['filename'] ) )
            return new JsonResponse([ 'error' => '515 Duplicate File' ]);

        elseif(preg_match("/(\w)*\.(\w)*/", pathinfo($file['name'])['filename'] ))
            return new JsonResponse([ 'error' => '516 Invalid Filename' ]);

        elseif($file['name'] === "" or $file['name'] === null)
            return new JsonResponse([ 'error' => '517 Empty Filename' ]);

        /*else if(strlen(pathinfo($file['name'])['filename']) < 5)
            return new Response("518 Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);*/

        list($width, $height) = getimagesize($file['tmp_name']);

        $root = $this->getParameter('project_dir') . '/../upload/source/' . $customerName . '/' . $splash[0] . '/' . $mediaType;

        if($height === 2160) // 4k
            $root .= '/HD/UHD-4k';

        /*else if($height === 4320) // 8k
            $root .= '/HD/UHD-8k';
        */

        if(!file_exists($root))
            mkdir($root,0777, true);

        $path = $root . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);
        // copy($path, $root . $customerName . '/' . $type . '/' . $file['name']);

        /*$root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $customerName . '/' . $mediaType . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);
        copy($path, $root . $customerName . '/' . $type . '/' . $file['name']);*/

        $explode = explode('.', $file['name']);
        $name = $explode[0];

        // if is image, insert in db
        if($splash[0] === 'image')
        {

            $taskInfo = [
                'fileName' => $name,
                'customerName' => $customerName,
                'mediaType' => $mediaType,
                'uploadDate' => new DateTime(),
                'extension' => $explode[1],
                'mediaProducts' => [],
                'mediaTags' => [],
                'mediaContainIncruste' => false,
                'mimeType' => $mimeType,
                'isArchived' => false,
                'height' => $height,
                'width' => $width,
            ];

            $uploadedImageFormatsCreator = new UploadedImageFormatsCreator($this->__parameterBag);
            $uploadedImageFormatsCreator->createImageFormats($taskInfo);

            if(!empty($uploadedImageFormatsCreator->getErrors()))
            {

                $errors = implode(' ; ', $uploadedImageFormatsCreator->getErrors());

                if(preg_match("/bad ratio/i", $errors ))
                    return new JsonResponse([ 'error' => '521 Bad ratio' ]);

                else
                    throw new Exception( sprintf("Internal Error : 1 or multiple errors during insert new image ! Errors : '%s'", $errors) );

            }
            else
                $media = $mediaRepository->insertImage($uploadedImageFormatsCreator->getImageInfos());

            $this->renameMediaWithId($media->getName(), $media->getId(), $uploadedImageFormatsCreator->getFilesToRenameList());

            $miniaturePath = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . "/image/" . $mediaType . '/low/' . $media->getId() . ".png";

//            if(!file_exists($miniaturePath))
//                throw new Exception(sprintf("Miniature file is not found ! This path is correct ? : '%s'", $miniaturePath));

            $dpi = $this->__mediasHandler->getImageDpi($miniaturePath);

            $response = [
                'id' => $media->getId(),
                'fileName' => $name,
                'fileNameWithoutExtension' => $media->getName(),
                'extension' => $media->getExtension(),
                'height' => $media->getHeight(),
                'width' => $media->getWidth(),
                'dpi' => $dpi,
                'miniatureExist' => file_exists($miniaturePath),
                'fileType' => 'image',
                'mediaType' => $mediaType,
                'customer' => $customerName,
            ];

        }

        else
        {

            $fileName = pathinfo($file['name'])['filename'] . '.' . pathinfo($file['name'])['extension'];
            $customer = $customerRepository->findOneByName( $customerName );

            if($fileType === 'video')
                $media = new Video();

            // need new Entity
            else
                throw new Exception(sprintf("Need new media type implementation for type '%s'", $fileType));


            if($type === 'synchros')
            {

                $synchroDatas = json_decode($request->request->get('synchro'));

                /*$synchro = new Synchro();
                $synchro->setName($synchroDatas->__name);*/

                $media = new SynchroElement();
                $media->setPosition($synchroDatas->__position);
                //->addSynchro($synchro);

            }

            $media->setName( str_replace( '.' . $real_file_extension, null, $fileName) )
                  ->setExtension($real_file_extension)
                  ->setMimeType($mimeType)
                  ->setContainIncruste(false)
                  ->setIsArchived(false)
                  ->setMediaType($mediaType);

            $media = json_decode($this->__serializer->serialize($media, 'json'), true);

            $media['createdAt'] = date('Y-m-d');

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

            list($width, $height, $codec) = $this->__mediasHandler->getVideoDimensions($path);

            // register Ffmpeg task
            // a CRON will do task after
            $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $this->__parameterBag);
            $id = $ffmpegSchedule->pushTask($fileInfo);

            $response = [
                'id' => $id,
                'fileName' => $fileName,
                'fileNameWithoutExtension' => str_replace( '.' . $real_file_extension, null, $fileName),
                'extension' => $real_file_extension,
                'height' => $height,
                'width' => $width,
                'codec' => $codec ?? null,
                'fileType' => 'video',
                'customer' => $customerName,
                'mimeType' => 'video/mp4',
                //'highestFormat' => $highestFormat,
            ];

        }

        return new JsonResponse($response, Response::HTTP_OK);

    }


    /**
     * @Route(path="/save/synchro/infos", name="media::saveSynchroInfos", methods={"POST"})
     */
    public function saveSynchroInfos(Request $request)
    {

        //dd($request->request);

        $customerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $synchroRep = $manager->getRepository(Synchro::class)->setEntityManager($manager);
        $synchroElementRep = $manager->getRepository(SynchroElement::class)->setEntityManager($manager);

        $arraySearchRecursive = new ArraySearchRecursiveService();

        $synchroElementIds = $synchroElementNames = $synchroElementPositions = $errors = [];

        $formData = $request->request->get('synchro_edit_form')['synchro'];

        $synchro = (array_key_exists('synchro_id', $formData) && !is_null($formData['synchro_id']) && !empty($formData['synchro_id'])) ? $synchroRep->find($formData['synchro_id']) : $synchroRep->findOneByName($formData['name']);

        if(!$synchro)
            $synchro = new Synchro();

        $synchro->setName($formData['name']);

        if( in_array($formData['name'], $this->__sessionManager->get('existed_synchro_names')) )
        {

            $errors[] = [
                'subject' => "synchro",
                'text' => "Duplicate synchro name"
            ];

        }

        elseif( (array_key_exists('synchro_id', $formData) && !is_null($formData['synchro_id']) && !empty($formData['synchro_id'])) && (intval($formData['synchro_id']) <= 0) )
        {

            $errors[] = [
                'subject' => "synchro",
                'text' => "Invalid synchro id"
            ];

        }

        $synchroElementNames = array_map(fn($synchroElementInfos) => $synchroElementInfos['name'] , $formData['synchros_elements'] );
        $synchroElementPositions = array_map(fn($synchroElementInfos) => $synchroElementInfos['position'] , $formData['synchros_elements'] );

        foreach ($formData['synchros_elements'] as $key => $synchroElementInfos)
        {

            $synchroElement = $synchroElementRep->find( $synchroElementInfos['synchro_element_id'] );
            if(!$synchroElement)
            {
                //throw new Exception(sprintf("No synchro element found with '%s' name", $synchroElementInfos['old_name']));
                $errors[] = [
                    'subject' => "synchro_element_" . ($key +1),
                    'text' => "Invalid synchro element id"
                ];
            }
            else
            {

                if( $arraySearchRecursive->countOccurrence($synchroElementInfos['name'], $synchroElementNames) === 1 )
                    $synchroElement->setName($synchroElementInfos['name']);

                else
                {
                    $errors[] = [
                        'subject' => "synchro_element_" . ($key +1),
                        'text' => "Duplicate synchro element name"
                    ];
                }


                if( $arraySearchRecursive->countOccurrence($synchroElementInfos['position'], $synchroElementPositions) === 1 )
                    $synchroElement->setPosition($synchroElementInfos['position']);

                else
                {
                    $errors[] = [
                        'subject' => "synchro_element_" . ($key +1),
                        'text' => "Position already used"
                    ];
                }

                if( false === $arraySearchRecursive->search("synchro_element_" . ($key +1), $errors) )
                {
                    $synchroElement->addSynchro($synchro);
                    $synchroElementIds[] = [
                        'element' => "synchro_element_" . ($key +1),
                        'id' => $synchroElement->getId(),
                    ];
                }

            }

        }

        if(empty($errors))
        {

            if(is_null($synchro->getId()))
                $manager->persist($synchro);

            $manager->flush();
        }

        $synchroInfos = [
            'synchro_id' => $synchro->getId(),
            'synchro_elements_infos' => $synchroElementIds
        ];

        return new JsonResponse([
                                    'status' => (empty($errors) ? '200 OK' : 'NOK'),
                                    'errors' => $errors,
                                    'synchro_infos' => $synchroInfos
                                ]);

    }


    /**
     * @Route(path="/get/video/encoding/status", name="media::getMediaEncodingStatus", methods={"POST"})
     * @throws Exception
     */
    public function getMediaEncodingStatus(Request $request, FfmpegTasksRepository $ffmpegTasksRepository)
    {

        $task = $ffmpegTasksRepository->find($request->request->get('id'));
        if(!$task)
            throw new Exception(sprintf("No Ffmpeg task found with id : '%d'", $request->request->get('id')));

        $customerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        // finish with 0 errors
        if($task->getFinished() !== null AND $task->getErrors() === null)
        {

            $media = $mediaRepository->findOneByName( $task->getMedia()['name'] );
            if(!$media)
                throw new Exception(sprintf("No Media found with name : '%s", $task->getMedia()['name']));

            $path = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . '/' . $media->getMediaType() . '/' . $media->getId() . ".mp4";

            //dd($path);

            $response = [
                'status' => 'Finished',
                'id' => $media->getId(),
                'miniatureExist' => file_exists($path),
                'extension' => $media->getExtension(),
                'fileName' => $task->getFilename(),
                'fileNameWithoutExtension' => $media->getName(),
                'height' => $media->getHeight(),
                'width' => $media->getWidth(),
                'codec' => $media->getVideoCodec(),
                'fileType' => 'video',
                'customer' => $customerName,
                'mimeType' => 'video/mp4',
                'name' => $media->getName()
            ];

        }

        // finish with 1 or more errors
        elseif($task->getFinished() !== null AND $task->getErrors() !== null)
        {

            /*if($task->getMediatype() === 'sync')
            {
                $taskMedia = $task->getMedia();

                $mediaRepository->removeUncompleteSynchros( $taskMedia['synchros'] );
            }*/

            $response = ['status' => 'Finished', 'type' => '520 Encode error', 'error' => $task->getErrors()];
        }

        // not finish
        else
            $response = ['status' => 'Running'];

        return new JsonResponse($response);

    }

    /**
     * @Route(path="/remove/media/{id}", name="media::removeMedia", methods={"POST", "GET"},
     * requirements={"id": "\d+"})
     */
    public function removeMedia(Request $request, int $id)
    {

        $customerName = $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        //dd($id);

        $media = $mediaRepository->find($id);
        if(!$media)
            throw new Exception(sprintf("No media found with id : '%s'", $id));

        //dd("ok");

        /*
        // delete source
        $root = $this->getParameter('project_dir') . '/../node_file_system/';
        $path = $root . $managerName . '/' . $media->getType() . '/' . $media->getName() . '.' . $media->getExtension();
        unlink($path);*/

        $fileType = explode("/", $media->getMimeType())[0];

        $sizes = ['low', 'medium', 'high', 'HD', 'high/UHD-4k', 'high/UHD-8k', 'HD/UHD-4k', 'HD/UHD-8k', 'HIGH/UHD-4k', 'HIGH/UHD-8k'];

        if($media->getMediaType() === 'elgp')
        {
            $path = $this->getParameter('project_dir') .'/../main/data_' . $customerName . '/PLAYER INFOWAY WEB/medias/piece/' . $media->getId() . '.' . ( ($fileType === 'image') ? '.png' : '.mp4' );
            if(file_exists($path))
                unlink($path);
        }

        else if( $media->getMediaType() === 'diff' )
        {

            foreach ($sizes as $size) {

                // medias; VIDÉOS; VIDÉOS HORIZONTALES; VIDÉOS VERTICALES; IMAGES/PRODUITS FIXES/PLEIN ECRAN/

                if( $media->getMediaType() === 'diff' )
                    $path = $this->getParameter('project_dir') .'/../main/data_' . $customerName . '/PLAYER INFOWAY WEB/medias/' . $fileType . '/' .$size .'/' . $media->getId() . '.' . ( ($fileType === 'image') ? '.png' : '.mp4' );

                if(file_exists($path))
                    unlink($path);
            }

        }

        else
        {

        }

        foreach ($sizes as $size) {

            // medias; VIDÉOS; VIDÉOS HORIZONTALES; VIDÉOS VERTICALES; IMAGES/PRODUITS FIXES/PLEIN ECRAN/

            if( $media->getMediaType() === 'diff' )
                $path = $this->getParameter('project_dir') .'/../main/data_' . $customerName . '/PLAYER INFOWAY WEB/medias/' . $fileType . '/' .$size .'/' . $media->getId() . '.' . ( ($fileType === 'image') ? '.png' : '.mp4' );

            else
            {
                // dossier 'AUTRES' pour 5asec,toujours utile ??
                $path = "";
            }

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
        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        $explode = explode('.', $request->request->get('file'));
        $fileNameWithoutExtension = $explode[0];

        $task = $tasksRepository->findOneBy(['filename' => $fileNameWithExtension, 'finished' => null]);

        // if file is in Media or if file is in FfmpegTasks but not already
        if($mediaRepository->findOneByName($fileNameWithoutExtension) OR $task)
            $output = 0;

        else
            $output = 1;

        return new Response( $output );
    }


    /**
     * @Route(path="/file/miniature/exists", name="media::mediaMiniatureExist", methods={"POST"})
     */
    public function mediaMiniatureExist(Request $request)
    {

        $path = $this->__parameterBag->get('project_dir'). '/public/' . $request->request->get('path');

        if( file_exists($path) )
            return new Response( "200 OK" );

        else
            return new Response( "404 File Not Found", 404 );
    }


    /**
     * @Route(path="/retrieve/media/associated/infos", name="media::retrieveMediaInfosForPopup", methods={"POST"})
     */
    public function retrieveMediaInfosForPopup(Request $request)
    {

        $mediaRepo = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) )
                          ->getRepository(Media::class);

        $mediaInfos = $mediaRepo->getMediaInfosForInfoSheetPopup(intval( $request->request->get('mediaId') ));

        return new JsonResponse($mediaInfos);
    }


    /**
     * @Route(path="/update/media/{id}/associated/{data}", name="media::updateMediaAssociatedData", methods={"POST"},
     * requirements={"id": "\d+", "data": "[a-z]+"})
     * @param Request $request
     * @param int $id
     */
    public function updateMediaAssociatedData(Request $request, int $id, string $data)
    {

        $manager = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) );
        $mediaRepo = $manager->getRepository(Media::class);
        $media = $mediaRepo->find($id);

        if(!$media)
            throw new Exception(sprintf("No media found with id : '%s'", $id));

        if($data === 'products')
            return $this->updateMediaAssociatedProducts($request->request->get('productsToAssociation'), $manager, $media);

        else if($data === 'tags')
            return $this->updateMediaAssociatedTags($request->request->get('tagsToAssociation'), $manager, $media);

        else
            throw new Exception(sprintf("Unrecognized 'data' parameter value ! Expected 'products' or 'tags', but '%s' given ", $data));

    }


    /**
     * @Route(path="get/media/{id}/programming/infos", name="media::getProgrammingInfos", methods={"POST"})
     */
    public function getProgrammingInfos(Request $request, int $id)
    {

        $manager = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) );
        $mediaRepo = $manager->getRepository(Media::class);
        $media = $mediaRepo->find($id);
        if(!$media)
            throw new Exception(sprintf("No media found with id : '%s'", $id));

        $customerName = strtolower($this->__sessionManager->get('current_customer')->getName());

        $mediaMiniatureExist = file_exists($this->__parameterBag->get('project_dir') . "/public/miniatures/" .
                                           $customerName. "/" . ( ($media instanceof Image) ? 'image': 'video') . "/low/" . $media->getId() . "."
                                           . ( ($media instanceof Image) ? 'png': 'mp4' ) );

        $mediaProgrammingMouldList = $this->__serializer->serialize($mediaRepo->getMediaProgrammingMouldList($media), 'json');

        return new JsonResponse([
                                    'mediaMiniatureExist' => $mediaMiniatureExist,
                                    'customer' => $customerName,
                                    'mediaProgrammingMouldList' => $mediaProgrammingMouldList
                                ]);

    }

    /**
     * @Route(path="/replace/media/in/{location}", name="media::replaceMedia", methods={"POST", "GET"}, requirements={"location": "mediatheque|programmation"})
     */
    public function replaceMedia(Request $request, string $location)
    {

        $manager = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) );
        $mediaRepo = $manager->getRepository(Media::class);

        $mediaToReplace = $mediaRepo->find($request->request->get('remplacementDatas')['mediaToReplaceId']);
        $mediaSubstitute = $mediaRepo->find($request->request->get('remplacementDatas')['substituteId']);

        if(!$mediaToReplace || !$mediaSubstitute)
            throw new Exception(sprintf("No media found with '%s' id !",
                                        (!$mediaToReplace) ? $request->request->get('remplacementDatas')['mediaToReplaceId'] :
                                            $request->request->get('remplacementDatas')['substituteId']));

        $fileType = $request->request->get('remplacementDatas')['fileType'];

        $currentCustomerName = strtolower($this->__sessionManager->get('current_customer')->getNAme());

        $remplacementDates = [
            'start' => $request->request->get('remplacementDatas')['remplacementDate']['start'],
            'end' => $request->request->get('remplacementDatas')['remplacementDate']['end'],
        ];

        $now = new DateTime('now');
        $mediaReplaceStartDate = new DateTime($remplacementDates['start']);

        dump($mediaReplaceStartDate === $now || $now > $mediaReplaceStartDate);

        if( $now >= $mediaReplaceStartDate )
        {

            $mediaRepo->replaceAllMediaOccurrences($mediaToReplace, $mediaSubstitute);

            /*$qualities = ['low', 'medium', 'high', 'HD'];;

            foreach ($qualities as $quality)
            {

                $source = $this->__parameterBag->get('project_dir') . "/../main/data_" . $currentCustomerName . "/PLAYER INFOWAY WEB/medias/"
                . $fileType . "/" . $quality . "/" . $mediaToReplace->getId() . "." . ( ($fileType === 'image') ? 'png': 'mp4' );

                if(file_exists($source))
                    unset($source);

            }*/

        }

        // @TODO: else remplacement à date (utiliser entité Date

        $manager->flush();

        dd($request->request);

    }


    /**
     * @param array $productsToAssociation
     * @param ObjectManager $manager
     * @param Media $media
     * @return Response
     * @throws Exception
     */
    private function updateMediaAssociatedProducts(array $productsToAssociation, ObjectManager &$manager, Media &$media)
    {

        $productRepo = $manager->getRepository(Product::class);

        $media->getProducts()->clear();

        foreach ($productsToAssociation as $productId)
        {

            $product = $productRepo->find($productId);
            if(!$product)
                throw new Exception(sprintf("No product found with id : '%s'", $productId));

            $media->addProduct($product);

        }

        //dd($media->getProducts()->getValues());

        $manager->flush();

        return new Response( "200 OK" );

    }

    /**
     * @param array $tagsToAssociation
     * @param ObjectManager $manager
     * @param Media $media
     */
    private function updateMediaAssociatedTags(array $tagsToAssociation, ObjectManager &$manager, Media &$media)
    {

        $tagRepo = $manager->getRepository(Tag::class);

        $media->getTags()->clear();

        foreach ($tagsToAssociation as $tagId)
        {

            $tag = $tagRepo->find($tagId);
            if(!$tag)
                throw new Exception(sprintf("No tag found with id : '%s'", $tagId));

            $media->addTag($tag);

        }

        //dd($media->getProducts()->getValues());

        $manager->flush();

        return new Response( "200 OK" );

    }

    /**
     * @Route(path="/update/mediatheque/medias/number", name="media::updateNumberOfMediasDisplayedInMediatheque", methods={"POST"})
     */
    public function updateNumberOfMediasDisplayedInMediatheque(Request $request)
    {

        $number = intval($request->request->get('mediatheque_medias_number'));

        if(!is_int($number) && $number <= 0)
            throw new Exception(sprintf("'mediatheque_medias_number' Session varaible cannot be update with '%s' value beacause it's not int!", $number));

        $this->__sessionManager->set('mediatheque_medias_number', $number);

        return new Response( "200 OK" );

    }

    /**
     * @Route(path="/save/upload/medias/infos", name="media::saveMediaCharacteristic", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function saveMediaCharacteristic(Request &$request)
    {

        $ffmpegTasksRepository = $this->getDoctrine()->getManager()->getRepository(FfmpegTasks::class);
        $customerRepository = $this->getDoctrine()->getManager()->getRepository(Customer::class);


        $manager = $this->getDoctrine()->getManager( strtolower( $this->__sessionManager->get('current_customer')->getName() ) );

        //dd($request->request, $customer);

        $cards = $error = [];

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

                //$mediaInfos['mediaType'] = 'video';

                $media = $manager->getRepository(Media::class)->setEntityManager($manager)->find( $mediaInfos['id'] );
                if(!$media)
                    throw new Exception(sprintf("No media can be found with this id : '%s'", $mediaInfos['id']));

                if( array_key_exists('diffusionStart', $mediaInfos) AND array_key_exists('diffusionEnd', $mediaInfos) )
                {

                    // check if date is valid and exist in gregorian calendar (@see: https://www.php.net/manual/en/function.checkdate.php)
                    // if date is not valid return new Response("519 Invalid diffusion date", Response::HTTP_INTERNAL_SERVER_ERROR);
                    // parameter order : ( int $month , int $day , int $year )
                    // form data order : year-month-day
                    if(!checkdate(substr($mediaInfos['diffusionStart'], 5, 2), substr($mediaInfos['diffusionStart'], 8, 2), substr($mediaInfos['diffusionStart'], 0, 4)))
                    {
                        $error = [ 'text' => '519.1 Invalid diffusion start date', 'subject' => $index ];
                        break;
                    }

                    if(!checkdate(substr($mediaInfos['diffusionEnd'], 5, 2) , substr($mediaInfos['diffusionEnd'], 8, 2), substr($mediaInfos['diffusionEnd'], 0, 4)))
                    {
                        $error = [ 'text' => '519.2 Invalid diffusion end date', 'subject' => $index ];
                        break;
                    }

                    $diffusionStartDate = new DateTime( $mediaInfos['diffusionStart'] );
                    $diffusionEndDate = new DateTime( $mediaInfos['diffusionEnd'] );

                    if($diffusionEndDate < $diffusionStartDate)
                    {
                        $error = [ 'text' => '519 Invalid diffusion date', 'subject' => $index ];
                        break;
                    }

                    $media->setDiffusionStart($diffusionStartDate)
                          ->setDiffusionEnd($diffusionEndDate)
                          ->setContainIncruste( $mediaInfos['containIncrustations'] );

                }

                $media->setName( $mediaInfos['name'] );

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
                    $categoriesId = [];
                    foreach ($mediaInfos['products'] as $k => $productId)
                    {
                        $productRepo = $manager->getRepository(Product::class)->setEntityManager($manager);
                        $product = $productRepo->find($productId);
                        if(!$product)
                            throw new Exception(sprintf("No Product found with id : '%d'", $productId));

                        // lors de l'association d'un produit
                        // le media recupère les tags du produit
                        foreach ($product->getTags()->getValues() as $tag)
                        {
                            $media->addTag($tag);
                        }

                        $media->addProduct($product);
                        if(null !== $product->getCategory())
                            $categoriesId[] = $product->getCategory()->getId();
                        $criterionsId = $productRepo->getProductsCriterionsIds($product);
                    }
                }

                $manager->flush();

                if($error === [])
                {

                    $cards[] = $this->buildNewMediaCard($mediaInfos, $media);

                    /*$response[] = [
                        'id' => $media->getId(),
                        'createdAt' => $media->getCreatedAt()->format('Y-m-d'),
                        'fileType' => ($media instanceof Image) ? 'image' : 'video',
                        'orientation' => $media->getOrientation(),
                        'diffStart' => $media->getDiffusionStart(),
                        'diffEnd' => $media->getDiffusionEnd(),
                        'customer' => $this->__sessionManager->get('current_customer')->getName(),
                        'products' => $mediaInfos['products'] ?? [],
                        'tags' => $mediaInfos['tags'] ?? [],
                        'categories' => $categoriesId ?? [],
                        'criterions' => $criterionsId ?? [],
                    ];*/

                }

            }

        }

        if($error === [])
        {
            return new JsonResponse( $cards );
        }

        return new JsonResponse( $error , Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getMediaHigestFormat(int $id, string $mediaType)
    {

        $formats = [
            'HD', 'high', 'medium', 'low'
        ];

        $customerName = $this->__sessionManager->get('current_customer')->getName();

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

        $numberOfMediasDisplayedInMediatheque = $this->__sessionManager->get('mediatheque_medias_number');

        $mediasDisplayedType = $request->get('mediasDisplayedType');

        $page = intval($request->get('page'));

        if($page < 1)
            $page = 1;

        if($mediasDisplayedType === "template")
        {
            die("TODO: get all templates");
        }

        elseif($mediasDisplayedType === "medias")
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque('diff', $page, $numberOfMediasDisplayedInMediatheque);

        elseif($mediasDisplayedType === "video_synchro")
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque('sync', $page, $numberOfMediasDisplayedInMediatheque);

        elseif($mediasDisplayedType === "video_thematic")
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque('them', $page, $numberOfMediasDisplayedInMediatheque);

        elseif($mediasDisplayedType === "element_graphic")
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque('elmt', $page, $numberOfMediasDisplayedInMediatheque);

        elseif($mediasDisplayedType === "incruste")
        {
            die("TODO: get all incrustes");
        }

        else
            $mediasToDisplayed = $mediaRepo->getMediaInByTypeForMediatheque($mediasDisplayedType, $page, $numberOfMediasDisplayedInMediatheque);

        $numberOfPages = $mediasToDisplayed['numberOfPages'];
        $numberOfMediasAllowedToDisplayed = $mediasToDisplayed['mediatheque_medias_number'];
        unset($mediasToDisplayed['numberOfPages']);
        unset($mediasToDisplayed['mediatheque_medias_number']);

        if($serializeResults)
        {
            $mediasToDisplayed = json_decode( $this->__serializer->serialize($mediasToDisplayed, 'json'), true);

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

    /**
     * Return an array which contain all filter content
     * @return array
     */
    private function getPopupFiltersContent()
    {

        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $productRepo = $manager->getRepository(Product::class)->setEntityManager( $manager );
        $categoryRepo = $manager->getRepository(Category::class)->setEntityManager( $manager );
        $tagRepo = $manager->getRepository(Tag::class)->setEntityManager( $manager );
        $criterionRepo = $manager->getRepository(Criterion::class)->setEntityManager( $manager );

        return [
            'products' => $productRepo->findAll(),
            'tags' => $tagRepo->findAll(),
            'categories' => $categoryRepo->findAll(),
            'criterions' => $criterionRepo->findAll(),
        ];

    }

    private function buildNewMediaCard(array &$mediaInfos, Media $media)
    {

        //$manager = $this->getDoctrine()->getManager( strtolower( $this->__sessionManager->get('current_customer')->getName() ) );
        //$mediaRepo = $manager->getRepository(Media::class)->setEntityManager($manager);

        $fileType = explode("/", $media->getMimeType())[0];
        $mediaName = $media->getName();
        $id = $media->getId();
        $mediaCreatedAt = $media->getCreatedAt()->format("Y-m-d");
        $mediaDiffStart = $media->getDiffusionStart()->format("Y-m-d");
        $mediaDiffEnd = $media->getDiffusionEnd()->format("Y-m-d");
        $orientation = strtolower( $media->getOrientation() );
        $mediaMimeType = $media->getMimeType();
        $width = $media->getWidth();
        $height = $media->getHeight();
        $extension = $media->getExtension();
        $criterionsNames = [];
        $tagsNamesAndColors = [];

        $customer = strtolower($this->__sessionManager->get('current_customer')->getName());

        $miniatureLowPath = "/miniatures/" . $customer . "/" . $fileType . $media->getMediaType() . "/low/";

        $miniatureLowPath .= $id . ( ($fileType === 'image') ? '.png' : '.mp4' );
        $miniatureMediumPath = str_replace("low","medium", $miniatureLowPath);

        $miniatureLowExist = (file_exists($miniatureLowPath)) ? 'true': 'false';
        $miniatureMediumExist = (file_exists($miniatureMediumPath)) ? 'true' : 'false';

        $now = new \DateTime();

        if($media->getDiffusionEnd() < new \DateTime()) $dateDiff = intval($media->getDiffusionEnd()->diff($now)->format("-%a"));

        else $dateDiff = intval($media->getDiffusionEnd()->diff($now)->format("%a"));



        $card =  "<div class='card card_$fileType' id='media_$id'  data-created_date='$mediaCreatedAt' data-file_type='$fileType' data-orientation='$orientation' data-media_diff_start='$mediaDiffStart' data-media_diff_end='$mediaDiffEnd' data-customer='$customer' ";

        if (sizeof($media->getProducts()->getValues()) > 0)
        {
            $mediaCategoriesId = array_map(fn($product) => $product->getCategorie()->getId(), $media->getProducts()->getValues());
            $mediaProductsCriterionsId = array_unique(array_map(fn($criterion) => $criterion->getId(), array_map(fn($product) => $product->getCriterions()->getValues(), $media->getProducts()->getValues())));
            $criterionsNames[] = array_unique(array_map(fn($criterion) => $criterion->Name(), array_map(fn($product) => $product->getCriterions()->getValues(), $media->getProducts()->getValues())));

            $card .= "data-products='" . implode(", ", $mediaInfos['products']) . "' data-categories='" . implode(", ", $mediaCategoriesId) . "'  data-criterions='" . implode(", ", $mediaProductsCriterionsId) . "'";

            dd($mediaCategoriesId, $mediaProductsCriterionsId);
        }
        else
            $card .= "data-products='none' data-categories='none' data-criterions='none'";

        if (sizeof($media->getTags()->getValues()) > 0)
        {
            $card .= "data-tags='" . implode(", ", array_map(fn($tag) => $tag->getId(), $media->getTags()->getValues())) . "'";
            $tagsNamesAndColors[] = array_unique(array_map(fn($tag) => ['name' => $tag->getName(), 'color' => $tag->getColor()], $media->getTags()->getValues()));
        }
        else
            $card .= "data-tags='none'";


        $card .= "<div class='card_header'>
                    <div class='select_media_input_container'>
                        <label class='container-input'>
                            <input type='checkbox' class='select_media_input'>
                            <span class='container-rdo-tags'></span>
                        </label>
                    </div>
            
                    <div class='media_actions_shortcuts_container'>";


        if($dateDiff <= 14)
        {
            $card .= "<div class='shortcut shortcut_diff_date_modification alert_date'>
                        <i class='far fa-clock'></i>
                      </div>";
        }
        else
        {
            $card .= "<div class='shortcut shortcut_diff_date_modification'>
                        <i class='far fa-clock'></i>
                      </div>";
        }

        $card .= "<div class='shortcut'>
                            <i class='fas fa-euro-sign'></i>
                        </div>
            
                        <div class='shortcut'>
                            <i class='fas fa-link shortcut_product_association'></i>
                        </div>
            
                        <div class='shortcut'>
                            <i class='fas fa-spinner'></i>
                        </div>
            
                    </div>";

        $card .= "<div class='card_body'> <div class='media_miniature_container media_$orientation' data-miniature_medium_exist='$miniatureMediumExist' data-size='$width*$height' data-extension='$extension'";

        if($fileType === 'image')
            $card .= "data-dpi='72' >";
        else
            $card .= "data-codec='" . $media->getVideoCodec(). "' >";

        if(!$miniatureLowExist)
            $card .= "<img class='media_miniature miniature_$fileType' src='/build/images/no-available-image.png' alt='/build/images/no-available-image.png'>";
        else
        {

            if($fileType === 'image')
            {
                $card .= "<div class='media_container_img'>
                            <div class='media_container_arrows show_expanded_miniature'>
                                <i class='fas fa-expand-arrows-alt'></i>
                            </div>
                            <img class='media_miniature miniature_image' src='$miniatureLowPath' alt='$miniatureLowPath'>
                        </div>";
            }
            else
            {
                $card .= "<div class='media_container_video'>
                            <div class='media_container_arrows show_expanded_miniature'>
                                <i class='fas fa-expand-arrows-alt'></i>
                            </div>
                            <video class='media_miniature miniature_video'>
                                <source src='$miniatureLowPath' type='$mediaMimeType'>
                            </video>
                        </div>";
            }

        }

        $card .= "<div class='media_name_container'>
                    <span class='media_name'>$mediaName</span>
                </div>";

        $card .= "<div class='media_associated_items_container'>
                    <div class='media_criterions_container associated_item'>";

        if(sizeof($criterionsNames) > 0)
        {
            foreach ($criterionsNames as $criterionName)
            {
                $card .= "<p class='criterion'><span></span>$criterionName</p>";
            }

            $card .= "</div>";
        }
        else
            $card .= "<p>0 critères </p></div>";

        if(sizeof($tagsNamesAndColors) > 0)
        {
            foreach ($tagsNamesAndColors as $tagsNameAndColor)
            {
                $name = $tagsNameAndColor['name'];
                $color = $tagsNameAndColor['color'];

                $card .= "<p class='tag container-tags'>
                            <span class='mini-cercle' style='background: $color;'></span>
                            <span class='current-tags-name'>$name</span>
                          </p>";
            }

            $card .= "</div></div>";
        }
        else
            $card .= "<p>0 tags </p></div></div>";

        return $card;

    }

    private function renameMediaWithId(string $mediaName, int $mediaId, array $filesToRenameWithId)
    {

        foreach ($filesToRenameWithId as $fileToRenameWithIdPath)
        {

            $path = str_replace($mediaName, $mediaId, $fileToRenameWithIdPath);

            if(!file_exists($fileToRenameWithIdPath))
                throw new Exception(sprintf("File not found : %s", $fileToRenameWithIdPath));

            rename($fileToRenameWithIdPath, $path);

        }

    }

}