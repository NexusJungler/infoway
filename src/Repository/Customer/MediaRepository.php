<?php

namespace App\Repository\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Display;
use App\Entity\Customer\Image;
use App\Entity\Customer\ImageElementGraphic;
use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Entity\Customer\Synchro;
use App\Entity\Customer\SynchroElement;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Video;
use App\Entity\Customer\VideoElementGraphic;
use App\Entity\Customer\VideoThematic;
use App\Repository\Admin\AllergenRepository;
use App\Repository\RepositoryTrait;
use App\Service\MediaInfosHandler;
use App\Service\SynchroInfosHandler;
use App\Service\VideoThematicInfosHandler;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PDO;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    private Serializer $serializer;

    private MediaInfosHandler $mediasHandler;

    private AllergenRepository $allergenRepository;

    private ParameterBagInterface $parameterBag;

    public function __construct(ManagerRegistry $registry, MediaInfosHandler $mediasHandler, AllergenRepository $allergenRepository, ParameterBagInterface $parameterBag)
    {
        parent::__construct($registry, Media::class);

        $circularReferenceHandlingContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $encoder =  new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $circularReferenceHandlingContext);
        $this->serializer = new Serializer( [ $normalizer ] , [ $encoder ] );

        $this->mediasHandler = $mediasHandler;
        $this->allergenRepository = $allergenRepository;
        $this->parameterBag = $parameterBag;
    }


    /**
     * Return all medias which is still used in app (not archived and where diffusionEnd is not past)
     *
     * @return Media[]
     */
    public function getAllMediasStillUsedInApp()
    {

        return $this->_em->createQueryBuilder()->select("m")->from(Media::class, "m")
                                      ->where("m.isArchived = false AND m.diffusionEnd > CURRENT_DATE()")
                                      ->orderBy("m.id", "ASC")
                                      ->getQuery()
                                      ->getResult();

    }


    /**
     * Return all tags which is associated with edia and media products
     * @param Media $media
     * @return Tag[]
     */
    public function getMediaAssociatedTags(Media $media)
    {

        $tags = [];

        foreach ($media->getTags()->getValues() as $tag)
        {
            $tags[] = $tag;
        }

        foreach ($media->getProducts()->getValues() as $product)
        {

            foreach ($product->getTags()->getValues() as $tag)
            {
                if(!in_array($tag, $tags))
                    $tags[] = $tag;
            }

        }

        return $tags;

    }


    /**
     * Renvoie un tableau contenant tout les médias qui doivent contenir des incrustes mais qui ne contiennent pas encore d'incruste
     * (boolean a true et tableau des incrustes vide)
     *
     * @return array
     */
    public function getMediasInWaitingListForIncrustes(): array
    {

        $medias = $this->_em->createQueryBuilder()->select("m")->from(Media::class, "m")
            ->leftJoin(Image::class, "i", "WITH", "m.id = i.id")
            ->leftJoin(Video::class, "v", "WITH", "m.id = v.id")
            ->andWhere("( (i.isArchived = false OR v.isArchived = false) AND (i.containIncruste = true AND i.incrustes IS EMPTY) ) OR (v.containIncruste = true AND v.incrustes IS EMPTY)")
            ->orderBy("m.id", "ASC")
            ->getQuery()
            ->getResult();

        foreach ($medias as $media)
        {

            $media->file_type = explode("/", $media->getMimeType())[0];

        }

        //dd($medias);

        return [
            'number' => sizeof($medias),
            'medias' => $medias
        ];

    }


    public function getAllArchivedMedias()
    {

        $medias = $this->findBy(['isArchived' => true]);

        foreach ($medias as $media)
        {

            $media->file_type = explode("/", $media->getMimeType())[0];

        }

        return [
            'number' => sizeof($medias),
            'medias' => $medias
        ];

    }


    public function getMediaInByTypeForMediatheque(string $type, int $currentPage = 1, int $limit = 15)
    {

        // recupère les medias qui n'ont pas et qui en contiennent, si ils doivent en contenir (boolean a true)
        switch ($type)
        {

            case "medias":
            case "diff":
                /*$dql = $this->_em->createQueryBuilder()->select("m")->from(Media::class, "m")
                                ->leftJoin(Image::class, "i", "WITH", "i.id = m.id")
                                ->leftJoin(Video::class, "v", "WITH", "v.id = m.id");*/
                $dql = $this->_em->createQueryBuilder()->select("m")->from(Media::class, "m")
                                 ->leftJoin(Image::class, "i", "WITH", "i.id = m.id")
                                 ->leftJoin(Video::class, "v", "WITH", "v.id = m.id");

                break;

            case "sync":
                $dql = $this->createQueryBuilder("m")
                            ->leftJoin(SynchroElement::class, "vs", "WITH", 'vs.id = m.id')
                            ->leftJoin(Video::class, "v", "WITH", 'v.id = m.id');
                break;

            case "them":
                $dql = $this->createQueryBuilder("m")
                            ->leftJoin(VideoThematic::class, "vt", "WITH", 'vt.id = m.id')
                            ->leftJoin(Video::class, "v", "WITH", 'v.id = m.id');
                break;

            case "elmt":
                $dql = $this->createQueryBuilder("m")
                                 ->leftJoin(ImageElementGraphic::class, "i", "WITH", "m.id = i.id")
                                 ->leftJoin(VideoElementGraphic::class, "v", "WITH", "m.id = v.id");
                break;

            default:
                throw new Exception(sprintf("Error : Unrecognized media type ! Trying to get medias with '%s' type but this media type is not exist ", $type));

        }

        $dql = $dql->where("m.mediaType = :type")
                   ->andWhere("m.isArchived = false")
                   ->andWhere("m.containIncruste = false AND m.incrustes IS EMPTY")
                   ->orWhere("m.containIncruste = true AND m.incrustes IS NOT EMPTY")
                   ->setParameter("type", $type)
                   ->orderBy("m.id", "ASC")
                   ->setFirstResult($limit * ($currentPage - 1))
                   ->setMaxResults($limit);

        //dd($dql->getQuery()->getResult());

        // pagination (see; https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html#pagination)
        $medias = ( new Paginator($dql, $fetchJoinCollection = true))->setUseOutputWalkers(false);

        $orderedMedias['numberOfPages'] = intval( ceil($medias->count() / $limit) );

        // sert pour choisir le nombre de media à afficher sur la page
        for($i = 5; $i <= $medias->count()+5; $i+=5)
        {
            $orderedMedias['mediatheque_medias_number'][] = $i;
        }

        $orderedMedias['medias'] = [];

        $customerName = $this->getEntityManager()->getConnection()->getDatabase();

        if($type !=='sync')
        {
            foreach ($medias as $index => $media)
            {

                $fileType = explode("/", $media->getMimeType())[0];

                $mediaMiniatureLowExist = file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                                                      $customerName. "/" . $fileType . "/" . $media->getMediaType() . '/low/' . $media->getId() . "."
                                                      . ( ($fileType === 'image') ? 'png': 'mp4' ) );

                $mediaMiniatureMediumExist = file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                                                         $customerName. "/" . $fileType . "/" . $media->getMediaType() . '/medium/' . $media->getId() . "."
                                                         . ( ($fileType === 'image') ? 'png': 'mp4' ) );

                $orderedMedias['medias'][$index] = [
                    'media' => null,
                    'fileType' => $fileType,
                    'mediaType' => $media->getMediaType(),
                    'productsIds' => [],
                    'tagsIds' => [],
                    'criterionsIds' => [],
                    'criterions' => [],
                    'categories' => [],
                    'categoriesIds' => [],
                    'miniatureLowExist' => $mediaMiniatureLowExist,
                    'miniatureMediumExist' => $mediaMiniatureMediumExist,
                ];

                if($fileType === "image")
                    $orderedMedias['medias'][$index]['dpi'] = "72";

                else
                    $orderedMedias['medias'][$index]['codec'] = $media->getVideoCodec();

                foreach ($media->getTags()->getValues() as $tag)
                {
                    $orderedMedias['medias'][$index]['tagsIds'][] = $tag->getId();
                }

                foreach ($media->getProducts()->getValues() as $product)
                {

                    $orderedMedias['medias'][$index]['productsIds'][] = $product->getId();

                    if($product->getCategory() AND !in_array($product->getCategory()->getId(), $orderedMedias['medias'][$index]['categories']))
                    {
                        $orderedMedias['medias'][$index]['categories'][] = $product->getCategory();
                        $orderedMedias['medias'][$index]['categoriesIds'][] = $product->getCategory()->getId();
                    }

                    foreach ($product->getCriterions()->getValues() as $criterion)
                    {
                        if(!in_array($criterion, $orderedMedias['medias'][$index]['criterions']))
                        {
                            $orderedMedias['medias'][$index]['criterions'][] = $criterion;
                            $orderedMedias['medias'][$index]['criterionsIds'][] = $criterion->getId();
                        }
                    }

                    foreach ($product->getTags()->getValues() as $tag)
                    {
                        if(!in_array($tag->getId(), $orderedMedias['medias'][$index]['tagsIds']))
                        {
                            $orderedMedias['medias'][$index]['tagsIds'][] = $tag->getId();
                        }
                    }

                }

                if($type === 'them')
                    $orderedMedias['medias'][$index]['theme'] = $media->getTheme();

                $orderedMedias['medias'][$index]['media'] = $media;

            }
        }
        else
        {
            $orderedMedias['medias'] = $this->formateSynchros($medias);
        }

        //dd($orderedMedias);

        return $orderedMedias;

    }

    public function getMediaInfosForInfoSheetPopup(int $mediaId)
    {

        return $this->getMediaInfos($mediaId);

    }

    public function getMediaInfosForEdit(int $mediaId)
    {
        // surcharger le retour de la fonction ?
        return $this->getMediaInfos($mediaId);

    }

    /**
     * @param array $exceptions
     * @return Media[]
     */
    public function getAllMediasExcept(array $exceptions)
    {

        $medias = [];

        foreach ($this->findAll() as $media)
        {
            if( !in_array($media, $exceptions) )
            {
                $customerName = $this->getEntityManager()->getConnection()->getDatabase();
                $fileType = explode("/", $media->getMimeType())[0];

                $path = $this->parameterBag->get('project_dir') . "/public/miniatures/" .
                    $customerName. "/" . $fileType. "/" . $media->getMediaType() . "/low/" . $media->getId() . "."
                    . ( ($fileType === 'image') ? 'png': 'mp4' );

                $media->media_low_miniature_exist = file_exists( $path );
                $media->miniature_medium_exist = file_exists( str_replace('low', 'medium', $path) );

                //miniature_medium_exist

                $media->fileType = $fileType;
                $medias[] = $media;
            }
        }

        return $medias;
    }

    public function getMediaProgrammingMouldList(Media $media)
    {

        return $this->_em->createQueryBuilder()->select("prog_moulds")
                                               ->distinct()
                                               ->from(ProgrammingMould::class, "prog_moulds")
                                               ->innerJoin(Display::class, 'display', 'WITH', 'prog_moulds.id = display.id')
                                               ->innerJoin(BroadcastSlot::class, 'broadcastSlot', 'WITH', 'broadcastSlot.display = display.id')
                                               ->innerJoin(ScreenPlaylist::class, 'screenPlaylist', 'WITH', 'screenPlaylist.id = broadcastSlot.id')
                                               ->innerJoin(ScreenPlaylistEntry::class, 'screenPlaylistEntry', 'WITH', 'screenPlaylistEntry.playlist = screenPlaylist.id')
                                               ->innerJoin(Media::class, 'media', 'WITH', 'media = :media')
                                               ->setParameter('media', $media)
                                               ->getQuery()
                                               ->getResult();

    }

    public function replaceAllMediaOccurrences(Media $mediaToReplace, Media $substitute, bool $deleteMediaToReplace = false)
    {

        $sql = "UPDATE media_product SET media_product.media_id = :substituteId WHERE media_product.media_id = :mediaToReplaceId;
                UPDATE media_tag SET media_tag.media_id = :substituteId WHERE media_tag.media_id = :mediaToReplaceId;
                UPDATE media_incruste SET media_incruste.media_id = :substituteId WHERE media_incruste.media_id = :mediaToReplaceId;
                UPDATE screen_playlist_entries SET screen_playlist_entries.media_id = :substituteId WHERE screen_playlist_entries.media_id = :mediaToReplaceId;
                UPDATE media SET media.is_archived = true WHERE media.id = :mediaToReplaceId
                ";

        if($deleteMediaToReplace)
            $sql .= "DELETE FROM media WHERE media.id = :oldMedia";

        return $this->_em->getConnection()->prepare($sql)
                         ->execute([
                                       'substituteId' => $substitute->getId(),
                                       'mediaToReplaceId' => $mediaToReplace->getId()
                                   ]);

    }

    public function insertVideo(array $videoInfos)
    {

        switch ($videoInfos['mediaType'])
        {

            case "diff":
                $media = new Video();
                break;

            case "them":
                $media = new VideoThematic();
                $media->setDate(new DateTime());
                break;

            case "elmt":
                $media = new VideoElementGraphic();
                $media->setContexte(null);
                break;

            case "sync":
                $media = new SynchroElement();

                /*$synchro = $this->_em->getRepository(Synchro::class)->findOneByName($videoInfos['synchros']['name']);

                if(!$synchro)
                {
                    $synchro = new Synchro();
                    $synchro->setName($videoInfos['synchros']['name']);
                    $this->_em->persist($synchro);
                }

                $media->addSynchro($synchro)
                      ->setPosition($videoInfos['position']);*/

                $media->setPosition($videoInfos['position']);

                break;

            default:
                throw new Exception( sprintf("Unrecognized mediaType : '%s'", $videoInfos['mediaType']) );

        }

        if(array_key_exists('audioCodec', $videoInfos) and array_key_exists('audioDebit', $videoInfos)
            and array_key_exists('audioFrequence', $videoInfos) and array_key_exists('audioChannel', $videoInfos)
            and array_key_exists('audioFrame', $videoInfos))
        {
            $media->setAudioCodec($videoInfos['audioCodec'])
                  ->setAudioDebit($videoInfos['audioDebit'])
                  ->setAudioFrequence($videoInfos['audioFrequence'])
                  ->setAudioChannel($videoInfos['audioChannel'])
                  ->setAudioFrame($videoInfos['audioFrame']);
        }

        $media->setSize($videoInfos['size'])
              ->setFormat($videoInfos['format'])
              ->setSampleSize($videoInfos['sampleSize'])
              ->setEncoder($videoInfos['encoder'])
              ->setVideoCodec($videoInfos['videoCodec'])
              ->setVideoCodecLevel($videoInfos['videoCodecLevel'])
              ->setVideoFrequence($videoInfos['videoFrequence'])
              ->setVideoFrame($videoInfos['videoFrame'])
              ->setVideoDebit($videoInfos['videoDebit'])
              ->setDuration($videoInfos['duration'])
              ->setMediaType($videoInfos['mediaType'])
              ->setWidth($videoInfos['width'])
              ->setHeight($videoInfos['height'])
              ->setIsArchived(false)
              ->setContainIncruste(false)
              ->setName($videoInfos['name'])
              ->setOrientation($videoInfos['orientation'])
              ->setMimeType($videoInfos['mimeType'])
              ->setRatio($videoInfos['ratio'])
              ->setExtension($videoInfos['extension'])
              ->setCreatedAt(new DateTime($videoInfos['createdAt']))
              ->setDiffusionStart( new DateTime($videoInfos['diffusionStart']) )
              ->setDiffusionEnd( new DateTime($videoInfos['diffusionEnd']) );

        /*array_map( fn($product) => $media->addProduct($product), $videoInfos['mediaProducts']);
        array_map( fn($tag) => $media->addTag($tag), $videoInfos['mediaTags']);*/

        $this->_em->persist($media);
        $this->_em->flush();

        return $media;

    }

    public function insertImage(array $imageInfos)
    {

        switch ($imageInfos['mediaType'])
        {

            case "diff":
                $media = new Image();
                break;

            case "elmt":
                $media = new ImageElementGraphic();
                $media->setContexte(null);
                break;

            default:
                throw new Exception( sprintf("Unrecognized mediaType : '%s'", $imageInfos['mediaType']) );

        }

        $media->setName($imageInfos['name'])
               ->setMediaType($imageInfos['mediaType'])
               ->setRatio($imageInfos['ratio'])
               ->setContainIncruste(false)
               ->setExtension($imageInfos['extension'])
               ->setOrientation($imageInfos['orientation'])
               ->setMimeType($imageInfos['mimeType'])
               ->setIsArchived(false)
               ->setWidth($imageInfos['width'])
               ->setHeight($imageInfos['height'])
               ->setSize($imageInfos['size'])
               ->setCreatedAt(new DateTime($imageInfos['createdAt']))
               ->setDiffusionStart( new DateTime($imageInfos['diffusionStart']) )
               ->setDiffusionEnd( new DateTime($imageInfos['diffusionEnd']) );

        $this->_em->persist($media);
        $this->_em->flush();

        return $media;
    }

    public function removeUncompleteSynchros( array $synchros )
    {

        foreach ($synchros as $synchroInfos)
        {

            $synchro = $this->_em->getRepository(Synchro::class)->findOneByName($synchroInfos['name']);
            if(!$synchro)
                throw new Exception(sprintf("No Synchro found with name : '%s'", $synchroInfos['name']));

            $this->_em->remove($synchro);
            $this->_em->flush();

        }

    }

    public function removeUncompletedSynchroElements(  )
    {



    }

    public function findAllNames()
    {
        $names = [];

        foreach ($this->findAll() as $media)
        {
            $names[] = $media->getName();
        }

        return $names;
    }

    public function getAllDisplayableMediasTypes(){

        $sql = "SELECT `media_type` FROM `media` WHERE `diffusable` GROUP BY `media_type`";
        $query = $this->getEntityManager()->getConnection()->prepare( $sql ) ;
        $query->execute( );
        return array_values($query->fetchAll(PDO::FETCH_COLUMN ));

    }

    private function getMediaInfos(int $mediaId)
    {

        $media = $this->find($mediaId);

        if(!$media)
            throw new Exception(sprintf("No media found with id : '%s'", $mediaId));

        $infos = [
            'mediaDiffStart' => $media->getDiffusionStart()->format('d/m/Y'),
            'mediaDiffEnd' => $media->getDiffusionEnd()->format('d/m/Y'),
            'mediaIncrustations' => [],
            'mediaProducts' => [],
            'mediaCriterions' => [],
            'mediaTags' => [],
            'mediaAllergens'=> [],
            'diffusionSpaces'=> [],
            'mediaType' => $media->getMediaType()
        ];

        foreach ($media->getTags()->getValues() as $tag)
        {
            if(!in_array($tag, $infos['mediaTags']))
                $infos['mediaTags'][] = $tag;
        }

        foreach ($media->getProducts()->getValues() as $product)
        {

            $infos['mediaProducts'][] = $product->getName();


            foreach ($product->getIncrustes()->getValues() as $incruste)
            {
                $infos['mediaIncrustations'][$product->getName()][] = $incruste->getTypeIncruste();
            }


            foreach ($product->getCriterions()->getValues() as $criterion)
            {
                if(!in_array($criterion->getName(), $infos['mediaCriterions']))
                    $infos['mediaCriterions'][] = $criterion->getName();
            }

            /*foreach ($product->getTags()->getValues() as $tag)
            {
                if(!in_array($tag->getName(), $infos['mediaTags']))
                    $infos['mediaTags'][] = $tag->getName();
            }*/

            foreach ($product->getAllergens()->getValues() as $allergen)
            {
                $allergenId = $allergen->getAllergenId();
                $allergen = $this->allergenRepository->find($allergenId);
                if(!$allergen)
                    throw new Exception(sprintf("No allergen found with id : '%s'", $allergenId));

                if(!in_array($allergen, $infos['mediaAllergens']))
                    $infos['mediaAllergens'][] = $allergen;
            }

        }

        //dd($infos);

        return $infos;

    }

    private function formateSynchros($medias)
    {

        $customer = $this->getEntityManager()->getConnection()->getDatabase();
        $synchros = [];

        foreach ($medias as $k => $synchroElement)
        {
            $synchros = array_values( array_unique( array_merge_recursive($synchros, array_map(fn($synchro) => $synchro, $synchroElement->getSynchros()->getValues())), SORT_REGULAR ) );
        }

        foreach ($synchros as &$synchro)
        {

            $synchroDiffStart = $synchroDiffEnd = "";
            $products = $tags = $criterions = $categories = $productsIds = $tagsIds = $criterionsIds = $categoriesIds = [];

            $synchro = json_decode( $this->serializer->serialize($synchro, 'json') );
            $date = new DateTime();
            $date->setTimestamp($synchro->createdAt->timestamp);
            $synchro->createdAt = $date->format('Y-m-d');
            $synchro->customer = $customer;

            $synchro->orientation = "";

            foreach ($synchro->synchroElements as $element)
            {

                $date->setTimestamp($element->createdAt->timestamp);
                $element->createdAt = $date->format('Y-m-d');

                $date->setTimestamp($element->diffusionStart->timestamp);
                $element->diffusionStart = $date->format('Y-m-d');

                $date->setTimestamp($element->diffusionEnd->timestamp);
                $element->diffusionEnd = $date->format('Y-m-d');

                $synchroDiffStart = $synchro->synchroElements[0]->diffusionStart;
                $synchroDiffEnd = end($synchro->synchroElements)->diffusionEnd;

                $element->lowMiniatureExist = file_exists( $this->parameterBag->get('project_dir') . "/public/miniatures/" . $customer . "/video/sync/low/" . $element->id. ".mp4" );

                //$productsIds = array_values( array_unique( array_merge( $productsIds, array_map(fn($product) => $product->id , $element->products ) ) ) );
                $tagsIds = array_values( array_unique( array_merge( $tagsIds, array_map(fn($tag) => $tag->id , $element->tags ) ) ) );

                foreach ($element->products as $product)
                {

                    if(!in_array($product, $products))
                        $products[] = $product;

                    if(!in_array($product->category, $categories))
                        $categories[] = $product->category;

                    if(!in_array($product->id, $productsIds))
                        $productsIds[] = $product->id;

                    if(!in_array($product->category->id, $categoriesIds))
                        $categoriesIds[] = $product->category->id;

                    $criterions = array_values( array_unique( array_merge( $criterions, array_map(fn($criterion) => $criterion , $product->criterions ) ),SORT_REGULAR ) );
                    $criterionsIds = array_values( array_unique( array_merge( $criterionsIds, array_map(fn($criterion) => $criterion->id , $product->criterions ) ) ) );

                }

            }

            $synchro->diffusionStart = $synchroDiffStart;
            $synchro->diffusionEnd = $synchroDiffEnd;
            $synchro->products = $products;
            $synchro->productIds = $productsIds;
            $synchro->tags = $tags;
            $synchro->tagIds = $tagsIds;
            $synchro->criterions = $criterions;
            $synchro->criterionIds = $criterionsIds;
            $synchro->categories = $categories;
            $synchro->categorieIds = $categoriesIds;

        }

        //dd($synchros);

        return $synchros;
    }

}