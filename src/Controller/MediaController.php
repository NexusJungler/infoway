<?php


namespace App\Controller;


use App\Entity\Admin\{ Customer, FfmpegTasks };
use App\Entity\Admin\{ ThematicTheme, VideoThematicThematicTheme };
use App\Entity\Customer\{ Category, Criterion, Image, Incruste, Media, Product, Synchro, SynchroElement, Tag, Video };
use App\Form\Customer\{ EditMediaType };
use App\Form\{ MediasListType };
use App\Repository\Admin\{ CustomerRepository, FfmpegTasksRepository, VideoThematicThematicThemeRepository };
use App\Service\ArraySearchRecursiveService;
use App\Service\{FfmpegSchedule,
    MediaFileManager,
    MediaInfosHandler,
    MediaToInsertDatasHandler,
    SessionManager,
    SynchroInfosHandler,
    UploadedImageFormatsCreator,
    VideoThematicInfosHandler};
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, Session\SessionInterface};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\{ SerializerInterface, Serializer };
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\{ AbstractNormalizer, ObjectNormalizer };
use \Doctrine\Persistence\ObjectManager;


class MediaController extends AbstractController
{

    private SerializerInterface $__serializer;

    private SessionManager $__sessionManager;

    private ParameterBagInterface $__parameterBag;

    private MediaInfosHandler $__mediasHandler;

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
        $this->__mediasHandler = new MediaInfosHandler($this->__parameterBag);
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
            $videoThematicThemesPrototype = "<option value=''>Choisir un thème</option>";

            foreach ($videoThematicThemeRep->findAll() as $theme)
            {
                $videoThematicThemesPrototype .= "<option value='" . $theme->getId() . "'>" .$theme->getName() . "</option>";
            }

            //dd($videoThematicThemesPrototype);

        }

        if($this->__sessionManager->get('mediatheque_medias_number') === null)
            $this->__sessionManager->set('mediatheque_medias_number', 5);

        list($mediasToDisplayed, $numberOfPages, $numberOfMediasAllowedToDisplayed) = $this->getMediasForMediatheque($manager, $request);

        if($mediasDisplayedType === "video_synchro")
        {

            $allSynchrosNames = $synchroRepo->findAllNames();

            $this->__sessionManager->set('existed_synchro_names', $allSynchrosNames);

        }
        else if( $mediasDisplayedType === "video_thematic" )
        {
            (new VideoThematicInfosHandler($this->getDoctrine()))->retrieveVideosThematics($mediasToDisplayed);
        }

        //dd($mediasToDisplayed);

        //if(empty($mediasToDisplayed))
        //    throw new NotFoundHttpException(sprintf("No media(s) found for this page !"));

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
            //'location' => $request->getPathInfo(),
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

        $fileType = explode("/", $media->getMimeType())[0];

        $mediaInfos = $mediaRepo->getMediaInfosForEdit($id);

        $medias = $mediaRepo->getAllMediasExcept([$media]);

        $form = $this->createForm(EditMediaType::class, $media, [
            'tagRepo' => $tagRepo,
            'mediaRepo' => $mediaRepo,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $manager->flush();
            //dd($media);

        }

        $mediaEditFormView = $form->createView();

        foreach($mediaEditFormView->children[ 'tags' ]->vars[ 'choices' ] as $choice )
        {
            $currentTag = $choice->data ;
            $mediaEditFormView->children['tags']->children[ $currentTag->getId() ]->vars['data'] = $currentTag ;
        }

        foreach($mediaEditFormView->children[ 'products' ]->vars[ 'choices' ] as $choice )
        {
            $currentProduct = $choice->data ;
            $mediaEditFormView->children['products']->children[ $currentProduct->getId() ]->vars['data'] = $currentProduct ;

        }

        $currentCustomerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );

        $path = $this->__parameterBag->get('project_dir') . "/public/miniatures/" . $currentCustomerName . "/" .
            $fileType . "/" . $media->getMediaType() . "/medium/". $media->getId() . "." .
            ( ($fileType === 'image') ? 'png' : 'mp4' );

        $miniature_medium_exist = file_exists($path);

        $miniature_low_exist = file_exists(str_replace('medium', 'low', $path));

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

        //dd($popupsFiltersContent);

        //dd($mediaEditFormView);

        return $this->render("media/edit_media.html.twig", [
            'products' => $popupsFiltersContent['products'],
            'tags' =>$popupsFiltersContent['tags'],
            'categories' => $popupsFiltersContent['categories'],
            'criterions' => $popupsFiltersContent['criterions'],
            'productsCriterions' => $productsCriterions,
            'productsTags' => $productsTags,
            'mediaInfos' => $mediaInfos,
            'form' => $mediaEditFormView,
            'media' => $media,
            'medias' => $medias,
            'fileType' => $fileType,
            'media_characteristics' => $characteristics,
            'media_incrustations' => $mediaInfos['mediaIncrustations'],
            'mediaCriterions' => $mediaInfos['mediaCriterions'],
            'mediaTags' => $mediaInfos['mediaTags'],
            'mediaAllergens' => $mediaInfos['mediaAllergens'],
            'action' => 'edit',
            'sousTitle' => 'Modifier',
            'miniature_medium_exist' => $miniature_medium_exist,
            'miniature_low_exist' => $miniature_low_exist,
        ]);

    }


    /**
     * @Route(path="/edit/synchro/{id}", name="media::editSynchro", methods={"GET", "POST"},
     * requirements={"id": "\d+"})
     */
    public function editSynchro(Request $request, int $id)
    {

        dd($request);

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

        if($type === 'synchro' and $fileType !== "video")
            return new JsonResponse([ 'error' => 'Invalid File type' ]);

        $customerName = strtolower( $this->__sessionManager->get('current_customer')->getName() );
        $manager = $this->getDoctrine()->getManager( $customerName );
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        if($this->__mediasHandler->fileIsCorrupt($file['tmp_name'], $fileType))
            return new JsonResponse([ 'error' => 'Corrupt File' ]);

        elseif(!in_array($real_file_extension, $this->getParameter("authorizedExtensions")))
            return new JsonResponse([ 'error' => 'Bad Extension' ]);

        elseif($mediaRepository->findOneByName( pathinfo($file['name'])['filename'] ) )
            return new JsonResponse([ 'error' => 'Duplicate File' ]);

        elseif(preg_match("/(\w)*\.(\w)*/", pathinfo($file['name'])['filename'] ))
            return new JsonResponse([ 'error' => 'Invalid Filename' ]);

        elseif($file['name'] === "" or $file['name'] === null)
            return new JsonResponse([ 'error' => 'Empty Filename' ]);

        /*else if(strlen(pathinfo($file['name'])['filename']) < 5)
            return new Response("Too short Filename", Response::HTTP_INTERNAL_SERVER_ERROR);*/

        //list($width, $height) = getimagesize($file['tmp_name']);

        $explode = explode('.', $file['name']);
        $name = $explode[0];
        $fileType = $splash[0];

        if($fileType === 'image')
            list($width, $height) = $this->__mediasHandler->getImageDimensions($file['tmp_name']);

        else
            list($width, $height, $codec) = $this->__mediasHandler->getVideoDimensions($file['tmp_name']);

        $root = $this->getParameter('project_dir') . '/../upload/source/' . $customerName . '/' . $fileType . '/' . $mediaType;

        if($height === 2160) // 4k
            $root .= '/HD/UHD-4k';

        else if($height === 4320) // 8k
            $root .= '/HD/UHD-8k';

        if(!file_exists($root))
            mkdir($root,0777, true);

        $path = $root . '/' . $file['name'];

        move_uploaded_file($file['tmp_name'], $path);

        // if is image, insert in db
        if($fileType === 'image')
        {

            $taskInfo = [
                'name' => $name,
                'customerName' => $customerName,
                'ratio' => "$width/$height",
                'mediaType' => $mediaType,
                'uploadDate' => new DateTime(),
                'extension' => $explode[1],
                'mediaContainIncruste' => false,
                'mimeType' => $mimeType,
                'isArchived' => false,
                'height' => $height,
                'width' => $width,
                'createdAt' => (new DateTime())->format("Y-m-d"),
                'diffusionStart' => (new DateTime())->format("Y-m-d"),
                'diffusionEnd' => (new DateTime())->modify("+10 year")->format("Y-m-d"),
            ];

            $uploadedImageFormatsCreator = new UploadedImageFormatsCreator($this->__parameterBag);

            $id = $uploadedImageFormatsCreator->createImageFormats($taskInfo);

            if(!empty($uploadedImageFormatsCreator->getErrors()))
            {

                $errors = implode(' ; ', $uploadedImageFormatsCreator->getErrors());

                if(preg_match("/bad ratio/i", $errors ))
                    return new JsonResponse([ 'error' => '521 Bad ratio' ]);

                else
                    throw new Exception( sprintf("Internal Error ! '%s'", $errors) );

            }
            else
                $media = $mediaRepository->insertImage($uploadedImageFormatsCreator->getImageInfos());

            $this->renameMediaWithId($media->getName(), $media->getId(), $uploadedImageFormatsCreator->getFilesToRenameList());

            $miniaturePath = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . "/image/" . $mediaType . '/low/' . $media->getId() . ".png";

            if(!file_exists($miniaturePath))
                throw new Exception(sprintf("Missing file : %s", $miniaturePath));

            $dpi = $this->__mediasHandler->getImageDpi($miniaturePath);

            $response = [
                'id' => $media->getId(),
                //'id' => $id,
                'fileName' => $name,
                'fileNameWithoutExtension' => $name,
                'extension' => $real_file_extension,
                'height' => $height,
                'width' => $width,
                'dpi' => $dpi,
                'miniatureExist' => file_exists($miniaturePath),
                'fileType' => 'image',
                'mediaType' => $mediaType,
                'customer' => $customerName,
                'cardsInfos' => []
            ];

            $response['cardsInfos'][] = [
                'id' => $media->getId(),
                'diffStart' => $media->getDiffusionStart()->format('Y-m-d'),
                'diffEnd' => $media->getDiffusionEnd()->format('Y-m-d'),
                'createdAt' => $media->getCreatedAt()->format("Y-m-d"),
                'orientation' => strtolower($media->getOrientation()),
                'name' => $name,
                'miniatureLowExist' => file_exists($miniaturePath),
                'miniatureMediumExist' => file_exists(str_replace('/low/', '/medium/', $miniaturePath)),
                'criterions' => [],
                'criterionsIds' => [],
                'productsIds' => [],
                'categoriesIds' => [],
                'tags' => [],
                'tagsIds' => [],
                'customer' => $customerName,
                'mediaType' => $mediaType,
                'fileType' => 'image',
                'extension' => $real_file_extension,
                'height' => $height,
                'width' => $width,
                'dpi' => $dpi,
            ];

        }

        else
        {

            $fileName = pathinfo($file['name'])['filename'] . '.' . pathinfo($file['name'])['extension'];

            $videoCharacteristics = $this->__mediasHandler->getVideoFileCharacteristics($path);
            $audioCharacteristics = null;
            if(array_key_exists('audio',$videoCharacteristics))
                $audioCharacteristics = $videoCharacteristics['audio'];

            $videoCharacteristics = $videoCharacteristics['video'];

            $level_array = str_split($videoCharacteristics['level']);
            $level = implode('.', $level_array);

            // quand on stocke l'objet dans la session, on obtient une erreur lorsque l'on fait $customer->addUploadTask() dans FfmpegSchedule
            // et lors du dump, on obtient un tableau vide avec le $customer->getUploadTasks()
            // donc on recupère l'objet depuis la base pour l'intégrité
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

            $media->setName( $name )
                  ->setOrientation(($width > $height) ? "Horizontal" : (($height > $width) ? "Vertical" : " "))
                  ->setSize( round($videoCharacteristics['size'] / (1024 * 1024), 2) . ' Mo' )
                  ->setFormat($videoCharacteristics['major_brand'])
                  ->setSampleSize( $videoCharacteristics['bits_per_raw_sample'] . ' bits' )
                  ->setEncoder( $videoCharacteristics['encoder'] )
                  ->setVideoCodec( $videoCharacteristics['codec_long_name'] )
                  ->setVideoCodecLevel($level)
                  ->setVideoFrequence( substr($videoCharacteristics['avg_frame_rate'], 0, -2) . ' img/s' )
                  ->setVideoFrame( $videoCharacteristics['nb_frames'] )
                  ->setVideoDebit( (int)($videoCharacteristics['bit_rate'] / 1000) . ' kbit/s' )
                  ->setDuration( round($videoCharacteristics['duration'], 2) . ' secondes' )
                  ->setCreatedAt( new DateTime() )
                  ->setDiffusionStart( new DateTime() )
                  ->setDiffusionEnd( (new DateTime())->modify("+10 year") )
                  ->setMimeType( $mimeType )
                  ->setRatio( "$width/$height" )
                  ->setAudioCodec( ($audioCharacteristics !== null) ? $audioCharacteristics['codec_long_name'] : null )
                  ->setAudioDebit( ($audioCharacteristics !== null) ? $audioCharacteristics['bit_rate'] : null )
                  ->setAudioFrequence( ($audioCharacteristics !== null) ? $audioCharacteristics['sample_rate'] : null )
                  ->setAudioChannel( ($audioCharacteristics !== null) ? $audioCharacteristics['channels'] : null )
                  ->setAudioFrame( ($audioCharacteristics !== null) ? $audioCharacteristics['nb_frames'] : null )
                  ->setExtension($explode[1])
                  ->setHeight($height)
                  ->setWidth($width)
                  ->setMimeType($mimeType)
                  ->setContainIncruste(false)
                  ->setIsArchived(false)
                  ->setMediaType($mediaType);

            $media = json_decode($this->__serializer->serialize($media, 'json'), true);

            $media['createdAt'] = date('Y-m-d');
            $media['diffusionStart'] = date('Y-m-d');
            $media['diffusionEnd'] = date('Y-m-d');

            $fileInfo = [
                'fileName' => $fileName,
                'customer' => $customer,
                'fileType' => $fileType,
                'type' => $mediaType,
                'extension' => $real_file_extension,
                'media' => $media,
                'mediaContainIncruste' => false,
                'isArchived' => false,
            ];

            // register Ffmpeg task
            // a CRON will do task after
            $ffmpegSchedule = new FfmpegSchedule($this->getDoctrine(), $this->__parameterBag);

            // on recupère l'id de la tache ffmped
            // pour pouvoir vérifier via ajax si elle est terminé
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
                'mediaType' => $mediaType,
                'customer' => $customerName,
                'mimeType' => 'video/mp4',
            ];

        }

        return new JsonResponse($response, Response::HTTP_OK);

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

            $mediaInfos = [];

            $media = $mediaRepository->findOneByName( $task->getMedia()['name'] );

            // si le media n'existe pas en base, on vérifie dans le fichier json
            // qui contient temporairement les infos des medias avant insertion (après caractérisation)
            if(!$media)
            {

                $pathToMediaToInsertInfos = $this->__parameterBag->get('project_dir') . '/..\upload\media_to_insert.json';
                $encodedVideosInfos = json_decode(file_get_contents($pathToMediaToInsertInfos),true);

                $index = (new ArraySearchRecursiveService())->search($task->getId(), $encodedVideosInfos);

                if($index !== false)
                {

                    $mediaInfos = $encodedVideosInfos[$index];
                    $mediaInfos['id'] = $index;

                }

                file_put_contents($pathToMediaToInsertInfos, json_encode($encodedVideosInfos));

            }
            else
            {
                $mediaInfos = json_decode($this->__serializer->serialize($media, 'json'), true);
            }

            if($mediaInfos !== [])
            {

                $path = $this->getParameter('project_dir') . "/public/miniatures/" . $customerName . '/video/' . $mediaInfos['mediaType'] . '/low/' . $mediaInfos['id'] . ".mp4";

                //dd($path);

                $response = [
                    'status' => 'Finished',
                    'id' => $mediaInfos['id'],
                    'orientation' => $mediaInfos['orientation'],
                    'miniatureExist' => file_exists($path),
                    'extension' => $mediaInfos['extension'],
                    'fileName' => $task->getFilename(),
                    'fileNameWithoutExtension' => $mediaInfos['name'],
                    'height' => $mediaInfos['height'],
                    'width' => $mediaInfos['width'],
                    'codec' => $mediaInfos['videoCodec'],
                    'fileType' => 'video',
                    'customer' => $customerName,
                    'mimeType' => 'video/mp4',
                    'mediaType' => $mediaInfos['mediaType'],
                    'name' => $mediaInfos['name'],
                    'error' => null
                ];

            }
            else
            {
                $response = [
                    'error' => "Media not found !"
                ];
            }

        }

        // finish with 1 or more errors
        elseif($task->getFinished() !== null AND $task->getErrors() !== null)
        {

            /*if($task->getMediatype() === 'sync')
            {
                $taskMedia = $task->getMedia();

                $mediaRepository->removeUncompleteSynchros( $taskMedia['synchros'] );
            }*/

            if( stristr($task->getErrors(), 'Incomplete encodage') )
                throw new Exception( sprintf("Internal Error ! '%s'", $task->getErrors()) );

            $response = ['status' => 'Finished', 'type' => 'Encode error', 'error' => $task->getErrors()];
        }

        // not finish
        else
            $response = ['status' => 'Running'];

        return new JsonResponse($response);

    }


    /**
     * @Route(path="/remove/media", name="media::removeMediaWithName", methods={"POST"})
     */
    public function removeMediaWithName(Request $request)
    {
        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);
        $mediaName = $request->request->get('mediaName');

        $media = $mediaRepository->findOneByName( $mediaName );
        if(!$media)
            throw new Exception(sprintf("No media found with name : %s", $mediaName));


        if(!$this->removeMedia($media))
            $status = "NOK";

        else
            $status = "200 OK";

        return new JsonResponse([ 'status' => $status ]);

    }


    /**
     * @Route(path="/remove/media/{id}", name="media::removeMediaWithId", methods={"POST", "GET"},
     * requirements={"id": "\d+"})
     */
    public function removeMediaWithId(Request $request, int $id)
    {

        if(!$this->removeMedia(null, $id))
            $status = "NOK";

        else
            $status = "200 OK";

        return new JsonResponse([ 'status' => $status ]);
    }


    /**
     * @Route(path="/remove/multiple/medias", name="media::removeMultipleMedias", methods={"POST"})
     */
    public function removeMultipleMedias(Request $request)
    {

        $mediasToDelete = $request->request->get('mediasToDelete');
        $mediaNotDeleted = [];

        foreach ($mediasToDelete as $mediaToDeleteId)
        {
            if(!$this->removeMedia($mediaToDeleteId))
            {
                //throw new Exception(sprintf("Error during deleting media with id '%d'", $mediaToDeleteId));
                $mediaNotDeleted[] = $mediaToDeleteId;
            }
        }

        return new JsonResponse([ 'status' => (empty($mediaNotDeleted)) ?  "200 OK" : "NOK", 'error' => $mediaNotDeleted ]);

    }


    /**
     * @Route(path="/remove/synchro/{id}", name="media::removeSynchroWithId", methods={"POST", "GET"})
     * @param Request $request
     * @param int $id
     */
    public function removeSynchroWithId(Request $request, int $id)
    {

        $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $synchroRepository = $manager->getRepository(Synchro::class)->setEntityManager($manager);

        $synchro = $synchroRepository->find( $id );
        $mediaNotDeleted = [];

        if(!$synchro)
            throw new Exception(sprintf("Not synchro found with id : %d", $id));

        foreach ($synchro->getSynchroElements()->getValues() as $synchroElement)
        {

            // suppression des videos dans le cas où elles ne sont pas utilisées dans d'autres synchros
            if(sizeof($synchroElement->getSynchros()->getValues()) < 2)
            {
                $synchroElement->removeSynchro($synchro);
                if(!$this->removeMedia($synchroElement))
                {
                    //throw new Exception(sprintf("Error during deleting media with id '%d'", $mediaToDeleteId));
                    $mediaNotDeleted[] = $synchroElement;
                }
            }

        }

        $manager->remove($synchro);
        $manager->flush();

        return new JsonResponse([ 'status' => (empty($mediaNotDeleted)) ?  "200 OK" : "NOK", 'error' => $mediaNotDeleted ]);
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
     * @Route(path="/replace/media", name="media::replaceMedia", methods={"POST"})
     */
    public function replaceMedia(Request $request)
    {
        //dd($request->request);
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
        else
        {
            dd("Remplacement a date");
        }

        // @TODO: else remplacement à date (utiliser entité Date

        $manager->flush();

        dd($request->request);

    }


    /**
     * @Route(path="/replace/synchro", name="media::replaceSynchro", methods={"POST"})
     */
    public function replaceSynchro(Request $request)
    {
        dd($request);
    }


    /**
     * @Route(path="/duplicate/media", name="media::duplicateMedia", methods={"POST"})
     */
    public function duplicateMedia(Request $request)
    {

        $mediasToDuplicateId = $request->request->get('mediasToDuplicateId');
        $customerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) );
        $mediaRepo = $manager->getRepository(Media::class);
        $response = [
            'status' => '200 OK',
            'ids' => [],
            'errors' => [],
        ];

        //dd($mediasToDuplicateId);

        foreach ($mediasToDuplicateId as $mediaToDuplicateId)
        {

            $media = $mediaRepo->find( $mediaToDuplicateId );
            if(!$media)
                throw new Exception(sprintf("No media found with id : %d", $mediaToDuplicateId));

            $newMedia = clone $media;

            //dd($newMedia, $media);

            // si un duplicat existe déjà
            // on boucle en incrementant $i pour créer le nom du duplicat
            if($mediaRepo->findOneByName( $newMedia->getName() . "_copy" ))
            {

                $i = 1;
                while ($mediaRepo->findOneByName( $newMedia->getName() . "_copy (" . $i . ")" ))
                {
                    $i++;
                }

                $newMedia->setName( $newMedia->getName() . "_copy (" . $i . ")" );

            }
            else
                $newMedia->setName( $newMedia->getName() . "_copy" );


            $manager->persist($newMedia);
            $manager->flush();

            $response['ids'][] = [
                'id' => $newMedia->getId(),
                'name' => $newMedia->getName()
            ];

            // copie des fichiers du media
            ( new MediaFileManager($this->__parameterBag, $this->__sessionManager, $media) )->duplicateMediaFiles($newMedia->getId());

        }

        //$manager->flush();

        //die;

        $response['status'] = (empty($response['errors'])) ? "200 OK" : "NOK";

        return new JsonResponse($response);

    }


    /**
     * @Route(path="/duplicate/synchro", name="media::duplicateSynchro", methods={"POST"})
     */
    public function duplicateSynchro(Request $request)
    {

        $synchrosToDuplicateId = $request->request->get('synchrosToDuplicateId');
        $manager = $this->getDoctrine()->getManager( strtolower($this->__sessionManager->get('current_customer')->getName()) );
        $synchroRepository = $manager->getRepository(Synchro::class)->setEntityManager($manager);
        $response = [
            'status' => '200 OK',
            'ids' => [],
            'errors' => [],
        ];
        //dd($synchrosToDuplicateId);

        foreach ($synchrosToDuplicateId as $mediaToDuplicateId)
        {

            $synchro = $synchroRepository->find( $mediaToDuplicateId );
            if(!$synchro)
                throw new Exception(sprintf("No synchro found with id : %d", $mediaToDuplicateId));

            $newSynchro = clone $synchro;

            //dd($newMedia, $media);

            // si un duplicat existe déjà
            // on boucle en incrementant $i pour créer le nom du duplicat
            if($synchroRepository->findOneByName( $newSynchro->getName() . "_copy" ))
            {

                $i = 1;
                while ($synchroRepository->findOneByName( $newSynchro->getName() . "_copy (" . $i . ")" ))
                {
                    $i++;
                }

                $newSynchro->setName( $newSynchro->getName() . "_copy (" . $i . ")" );

            }
            else
                $newSynchro->setName( $newSynchro->getName() . "_copy" );


            $manager->persist($newSynchro);
            $manager->flush();

            $response['ids'][] = [
                'id' => $newSynchro->getId(),
                'name' => $newSynchro->getName()
            ];

        }

        $response['status'] = (empty($response['errors'])) ? "200 OK" : "NOK";

        return new JsonResponse($response);

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

        //$ffmpegTasksRepository = $this->getDoctrine()->getManager()->getRepository(FfmpegTasks::class);
        //$customerRepository = $this->getDoctrine()->getManager()->getRepository(Customer::class);

        $response = [
            'status' => '200 OK',
            'medias' => [],
            'cardsInfos' => [],
            'errors' => []
        ];

        if($request->request->get('medias_list') === null)
            return new JsonResponse( $response );

        $manager = $this->getDoctrine()->getManager( strtolower( $this->__sessionManager->get('current_customer')->getName() ) );

        //dd($request->request);



        $isNewMedia = false;

        foreach ($request->request->get('medias_list')['medias'] as $index => $mediaInfos)
        {

            if(preg_match("/(\w)*\.(\w)*/", $mediaInfos['name']))
            {
                // return new Response("516 Invalid Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $response['errors'][] = [ 'text' => 'Invalid Filename', 'subject' => $index ];
                break;
            }

            elseif($mediaInfos['name'] === "" or $mediaInfos['name'] === null)
            {
                // return new Response("517 Empty Filename", Response::HTTP_INTERNAL_SERVER_ERROR);
                $response['errors'][] = [ 'text' => 'Empty Filename', 'subject' => $index ];
                break;
            }

            else
            {


                $media = $manager->getRepository(Media::class)->setEntityManager($manager)->find( $mediaInfos['id'] );

                if(!$media)
                    throw new Exception(sprintf("No Media not found with id '%s' !", $mediaInfos['id']));

                if($media->getMediaType() === "them")
                {

                    if(intval($mediaInfos['thematic']) <= 0)
                    {
                        $response['errors'][] = [ 'text' => 'Invalid thematic', 'subject' => $index ];
                        //throw new Exception(sprintf("Invalid thematic id given ! Can't parse to int thematic id ('%s' given)", $mediaInfos['thematic']));
                    }
                    else
                    {
                        $thematicTheme = $this->getDoctrine()->getManager('default')->getRepository(ThematicTheme::class)->find( $mediaInfos['thematic'] );
                        if(!$thematicTheme)
                            throw new Exception(sprintf("No thematic found with id '%s'", $mediaInfos['thematic']));

                        $this->getDoctrine()->getManager('default')->getRepository(VideoThematicThematicTheme::class)->updateVideoThematicTheme($media, $thematicTheme);

                    }

                }

                if( array_key_exists('diffusionStart', $mediaInfos) AND array_key_exists('diffusionEnd', $mediaInfos) )
                {

                    // check if date is valid and exist in gregorian calendar (@see: https://www.php.net/manual/en/function.checkdate.php)
                    // if date is not valid return new Response("519 Invalid diffusion date", Response::HTTP_INTERNAL_SERVER_ERROR);
                    // parameter order : ( int $month , int $day , int $year )
                    // form data order : year-month-day
                    if(!checkdate(substr($mediaInfos['diffusionStart'], 5, 2), substr($mediaInfos['diffusionStart'], 8, 2), substr($mediaInfos['diffusionStart'], 0, 4)))
                    {
                        $response['errors'][] = [ 'text' => 'Invalid diffusion start date', 'subject' => $index ];
                        break;
                    }

                    if(!checkdate(substr($mediaInfos['diffusionEnd'], 5, 2) , substr($mediaInfos['diffusionEnd'], 8, 2), substr($mediaInfos['diffusionEnd'], 0, 4)))
                    {
                        $response['errors'][] = [ 'text' => 'Invalid diffusion end date', 'subject' => $index ];
                        break;
                    }

                    $diffusionStartDate = new DateTime( $mediaInfos['diffusionStart'] );
                    $diffusionEndDate = new DateTime( $mediaInfos['diffusionEnd'] );

                    if($diffusionEndDate < $diffusionStartDate)
                    {
                        $response['errors'][] = [ 'text' => 'Invalid diffusion date', 'subject' => $index ];
                        break;
                    }

                    $media->setDiffusionStart($diffusionStartDate)
                          ->setDiffusionEnd($diffusionEndDate);

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

                if(array_key_exists('containIncrustations', $mediaInfos))
                    $media->setContainIncruste( $mediaInfos['containIncrustations'] );

                $media->setName( $mediaInfos['name'] );

                $manager->flush();

                if(!array_key_exists($index, $response['errors']) )
                {

                    //$response['cards'][$index] = $this->buildNewMediaCard($mediaInfos, $media);

                    $response['medias'][$index] = [
                        'id' => $media->getId()
                    ];

                    $customer = strtolower($this->__sessionManager->get('current_customer')->getName());
                    $criterionsIds = $categoriesIds = $criterions = [];
                    $fileType = explode("/", $media->getMimeType())[0];
                    $tags = array_map(fn($tag) => $tag , $media->getTags()->getValues());

                    foreach ($media->getProducts()->getValues() as $product)
                    {
                        $criterions = array_map(fn($criterion) => $criterion , $product->getCriterions()->getValues() );
                        $criterionsIds = array_map(fn($criterion) => $criterion->getId() , $product->getCriterions()->getValues() );
                        $categoriesIds[] = $product->getcategory()->getId();
                    }

                    //file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                    //                                                      $customerName. "/" . $fileType . "/" . $media->getMediaType() . '/low/' . $media->getId() . "."
                    //                                                      . ( ($fileType === 'image') ? 'png': 'mp4' ) )

                    $root = $this->getParameter('project_dir') . "/public/miniatures/" . $customer . "/" . $fileType . "/" . $media->getMediaType();
                    $miniatureLowExist = file_exists($root . "/low/" . $media->getId() . ( ($fileType === 'image') ? '.png': '.mp4' ));
                    $miniatureMediumExist = file_exists($root . "/medium/" . $media->getId() . ( ($fileType === 'image') ? '.png': '.mp4' ));

                    $response['cardsInfos'][$index] = [
                        'id' => $media->getId(),
                        'name' => $media->getName(),
                        'fileType' => $fileType,
                        'mediaType' => $media->getMediaType(),
                        'orientation' => strtolower($media->getOrientation()),
                        'customer' => $customer,
                        'width' => $media->getWidth(),
                        'height' => $media->getHeight(),
                        'extension' => $media->getExtension(),
                        'tags' => json_decode($this->__serializer->serialize($tags, 'json'), true),
                        'tagsIds' => array_map(fn($tag) => $tag->getId(), $media->getTags()->getValues()),
                        'productsIds' => array_map(fn($product) => $product->getId(), $media->getProducts()->getValues()),
                        'criterions' => json_decode($this->__serializer->serialize($criterions, 'json'), true),
                        'criterionsIds' => $criterionsIds,
                        'categoriesIds' => $categoriesIds,
                        'miniatureLowExist' => $miniatureLowExist,
                        'miniatureMediumExist' => $miniatureMediumExist,
                        'createdAt' => $media->getCreatedAt()->format('Y-m-d'),
                        'diffStart' => $media->getDiffusionStart()->format('Y-m-d'),
                        'diffEnd' => $media->getDiffusionEnd()->format('Y-m-d'),
                    ];

                    if($media->getMediaType() === "them")
                        $response['cardsInfos'][$index]['thematicName'] = $thematicTheme->getName();

                    if($fileType === 'image')
                        $response['cardsInfos'][$index]['dpi'] = 72;

                    else
                        $response['cardsInfos'][$index]['codec'] = $media->getVideoCodec();

                }

            }

        }

        $response['status'] = (empty($response['errors'])) ? "200 OK" :"NOK";

        return new JsonResponse( $response );
    }

    /**
     * @Route(path="/save/synchro/infos", name="media::saveSynchroInfos", methods={"POST"})
     */
    public function saveSynchroInfos(Request &$request)
    {

        //dd($request->request);

        $response = [
            'status' => '200 OK',
            'errors' => [],
            'synchroInfos' => [],
            'synchroElementsPreviews' => []
        ];

        if( $request->request->get('synchro_edit_form') === null )
            return new JsonResponse($response);

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
                    /*$synchroElementIds[] = [
                        'element' => "synchro_element_" . ($key +1),
                        'id' => $synchroElement->getId(),
                    ];*/
                }

            }

        }

        if(empty($errors))
        {

            if(is_null($synchro->getId()))
                $manager->persist($synchro);

            $manager->flush();

            list($synchroDiffStart, $synchroDiffEnd) = ( new SynchroInfosHandler() )->getSynchroDiffDates($synchro);

            $productsIds = $tagsIds = $criterionsIds = $categoriesIds = $synchroElementPreviews = [];

            foreach ($synchro->getSynchroElements()->getValues() as $k => $synchroElement)
            {

                $id = $synchroElement->getId();

                $synchroElementPreviews[] = "<div class='synchro_element' data-id='$id'> 
                                             <video>
                                                <source src='/miniatures/$customerName/video/sync/low/$id.mp4' type='video/mp4'>
                                             </video> 
                                         </div>";

                $tagsIds = array_values( array_unique( array_merge($tagsIds, array_map( fn($tag) => $tag->getId(), $synchroElement->getTags()->getValues() )) ) );

                foreach ($synchroElement->getProducts()->getValues() as $i => $product)
                {
                    $productsIds[] = $product->getId();
                    $categoriesIds[] = $product->getCategory()->getId();

                    $criterionsIds = array_values( array_unique( array_merge($criterionsIds, array_map( fn($criterion) => $criterion->getId(), $product->getCriterions()->getValues() )) ) );

                }

            }

            //dd($productsIds, $tagsIds, $criterionsIds, $categoriesIds);

            $synchroInfos = [
                'id' => $synchro->getId(),
                'name' => $synchro->getName(),
                //'synchro_elements_infos' => $synchroElementIds,
                'createdAt' => $synchro->getCreatedAt()->format('Y-m-d'),
                'customer' => $customerName,
                'diffStart' => $synchroDiffStart,
                'diffEnd' => $synchroDiffEnd,
                'products' => $productsIds,
                'tags' => $tagsIds,
                'criterions' => $criterionsIds,
                'categories' => $categoriesIds
            ];

        }
        else
        {
            $synchroInfos = $synchroElementPreviews = [];
        }

        $response = [
            'status' => (empty($errors) ? '200 OK' : 'NOK'),
            'errors' => $errors,
            'synchroInfos' => $synchroInfos,
            'synchroElementsPreviews' => $synchroElementPreviews
        ];

        //dd($response);

        return new JsonResponse($response);

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

    /**
     * Fonction servant à supprimer un media et ses fichiers
     *
     * @param int|null $id
     * @param Media|null $media
     * @return bool
     * @throws Exception
     */
    private function removeMedia(Media $media = null, int $id = null)
    {

        if(is_null($id) && is_null($media))
            throw new Exception("No id or media given !");

        $customerName = $managerName = strtolower($this->__sessionManager->get('current_customer')->getName());
        $manager = $this->getDoctrine()->getManager($managerName);
        $mediaRepository = $manager->getRepository(Media::class)->setEntityManager($manager);

        if($id)
        {

            $media = $mediaRepository->find($id);
            if(!$media)
                throw new Exception(sprintf("No media found with id : '%s'", $id));

        }

        //dd($media);

        ( new MediaFileManager($this->__parameterBag, $this->__sessionManager, $media) )->removeMediaFiles();

        $manager->remove($media);
        $manager->flush();

        return true;
    }

}