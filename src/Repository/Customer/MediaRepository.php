<?php

namespace App\Repository\Customer;

use App\Entity\Customer\BroadcastSlot;
use App\Entity\Customer\Display;
use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use App\Entity\Customer\ProgrammingMould;
use App\Entity\Customer\ScreenPlaylist;
use App\Entity\Customer\ScreenPlaylistEntry;
use App\Entity\Customer\Tag;
use App\Entity\Customer\Video;
use App\Repository\Admin\AllergenRepository;
use App\Repository\MainRepository;
use App\Service\MediasHandler;
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

    use MainRepository;

    private Serializer $serializer;

    private MediasHandler $mediasHandler;

    private AllergenRepository $allergenRepository;

    private ParameterBagInterface $parameterBag;

    public function __construct(ManagerRegistry $registry, MediasHandler $mediasHandler, AllergenRepository $allergenRepository, ParameterBagInterface $parameterBag)
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

            if($media instanceof Image)
            {
                $media->file_type = 'image';
            }

            else
            {
                $media->file_type = 'video';
            }

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

        return [
            'number' => sizeof($medias),
            'medias' => $medias
        ];

    }


    public function getMediaInByTypeForMediatheque(string $type, int $currentPage = 1, int $limit = 15)
    {
        //dd($type, $currentPage, $limit);
        // pagination (see; https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html#pagination)

        switch ($type)
        {

            // recupère les medias qui ont (boolean a true et tableau des incrustes non vide) et qui n'ont pas
            // d'incruste(boolean a false et tableau des incrustes vide)
            case "medias":

                $dql = "SELECT m FROM " . Media::class. " m 
                
                        LEFT JOIN " . Image::class . " i WITH m.id = i.id
                        LEFT JOIN " . Video::class . " v WITH m.id = v.id
                        
                        WHERE m.mediaType = 'diff' AND ( (i.isArchived = false OR v.isArchived = false) AND (i.containIncruste = false OR v.containIncruste = false) ) 
                        OR ( (i.containIncruste = true AND i.incrustes IS NOT EMPTY) AND (v.containIncruste = true AND v.incrustes IS NOT EMPTY) ) 
                        
                        ORDER BY m.createdAt DESC";

                $medias = $this->paginate($dql, $currentPage, $limit);

                //$orderedMedias['medias_products_criterions'] = $this->getEntityManager()->getRepository(Product::class)->findProductsCriterions();

                break;

            case "synchros":
                die("Need implementation for video synchro recuperation");
                break;

            case "thematics":
                die("Need implementation for video thematic recuperation");
                break;

            case "elgp":
                $dql = "SELECT m FROM " . Media::class. " m 
                
                        LEFT JOIN " . Image::class . " i WITH m.id = i.id
                        LEFT JOIN " . Video::class . " v WITH m.id = v.id
                        
                        WHERE m.mediaType = 'elmt' AND ( (i.isArchived = false OR v.isArchived = false) AND (i.containIncruste = false OR v.containIncruste = false) ) 
                        OR ( (i.containIncruste = true AND i.incrustes IS NOT EMPTY) AND (v.containIncruste = true AND v.incrustes IS NOT EMPTY) )
                        
                        ORDER BY m.createdAt DESC";

                $medias = $this->paginate($dql, $currentPage, $limit);
                break;

            default:
                throw new Exception(sprintf("Error : Unrecognized media type ! Trying to get medias with '%s' type but this media type is not exist ", $type));

        }

        $orderedMedias['numberOfPages'] = intval( ceil($medias->count() / $limit) );

        // sert pour choisir le nombre de media à afficher sur la page
        for($i = 5; $i <= $medias->count()+5; $i+=5)
        {
            $orderedMedias['mediatheque_medias_number'][] = $i;
        }

        $orderedMedias['medias'] = [];

        $customerName = $this->getEntityManager()->getConnection()->getDatabase();

        foreach ($medias as $index => $media)
        {

            if($media->getMediaType() === 'diff')
                $miniatureFolder = ( ($media instanceof Image) ? 'image': 'video') . '/low';

            elseif($media->getMediaType() === 'elmt')
                $miniatureFolder = "piece";

            else
                dd(sprintf("Need implementation for found '%s' miniature folder", $media->getMediaType()));

            $mediaMiniatureLowExist = file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                                                  $customerName. "/" . $miniatureFolder . "/" . $media->getId() . "."
                                                  . ( ($media instanceof Image) ? 'png': 'mp4' ) );

            $mediaMiniatureMediumExist = file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                                                     $customerName. "/"  . $miniatureFolder . "/" . $media->getId() . "."
                                                     . ( ($media instanceof Image) ? 'png': 'mp4' ) );

            $orderedMedias['medias'][$index] = [
                'media' => null,
                'file_type' => ($media instanceof Image) ? 'image': 'video',
                'media_type' => $media->getMediaType(),
                'media_products' => [],
                'media_tags' => [],
                'media_criterions' => [],
                'media_categories' => [],
                'miniature_low_exist' => $mediaMiniatureLowExist,
                'miniature_medium_exist' => $mediaMiniatureMediumExist,
            ];

            foreach ($media->getTags()->getValues() as $tag)
            {
                $orderedMedias['medias'][$index]['media_tags'][] = $tag->getId();
            }

            foreach ($media->getProducts()->getValues() as $product)
            {

                $orderedMedias['medias'][$index]['media_products'][] = $product->getId();

                if($product->getCategory() AND !in_array($product->getCategory()->getId(), $orderedMedias['medias'][$index]['media_categories']))
                    $orderedMedias['medias'][$index]['media_categories'][] = $product->getCategory()->getId();

                foreach ($product->getCriterions()->getValues() as $criterion)
                {
                    $orderedMedias['medias'][$index]['media_criterions'][] = $criterion->getId();
                }

                foreach ($product->getTags()->getValues() as $tag)
                {
                    if(!in_array($tag->getId(), $orderedMedias['medias'][$index]['media_tags']))
                        $orderedMedias['medias'][$index]['media_tags'][] = $tag->getId();
                }

                foreach ($product->getAllergens()->getValues() as $allergen)
                {
                    if(!in_array($allergen->getAllergenId(), $orderedMedias['medias'][$index]['media_tags']))
                        $orderedMedias['medias'][$index]['media_tags'][] = $allergen->getAllergenId();
                }

            }

            $orderedMedias['medias'][$index]['media'] = $media;

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
                $media->media_low_miniature_exist = file_exists($this->parameterBag->get('project_dir') . "/public/miniatures/" .
                                                                $customerName. "/" . ( ($media instanceof Image) ? 'image': 'video') . "/low/" . $media->getId() . "."
                                                                . ( ($media instanceof Image) ? 'png': 'mp4' ) );

                $media->file_type = ($media instanceof Image) ? 'image': 'video';
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


    public function getPriceValue($columnPrice, $id_pro, $grprix_data_id) {
        $column = 'PRO_'.$columnPrice.'_jour';
        $sql = "SELECT $column FROM `qui_groupe_prix_valeurs` WHERE id_produit = ? AND id_groupe_prix = ? and date = (
    SELECT MAX(date)
    FROM `qui_groupe_prix_valeurs`
    LIMIT 1
  )";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id_pro, $grprix_data_id));
        $row = $query->fetch(PDO::FETCH_NUM);
        $result = $row[0];
        return $result;
    }

    public function getAllDisplayableMediasTypes(){

        $sql = "SELECT `media_type` FROM `media` WHERE `diffusable` GROUP BY `media_type`";
        $query = $this->getEntityManager()->getConnection()->prepare( $sql ) ;
        $query->execute( );
        return array_values($query->fetchAll(PDO::FETCH_COLUMN ));

    }

    public function selectAllImagesAndVideosFromMedia()
    {
        try
        {
            $q = $this->_em->getConnection()->prepare("SELECT i.extension, m.id, m.filename FROM `media` as m LEFT JOIN `image` as i on i.media = m.id WHERE `type`='diff' AND i.extension = 'png'
UNION
SELECT v.extension, m.id, m.filename FROM `media` as m LEFT JOIN `video` as v on v.media = m.id WHERE `type`='diff' AND v.extension = 'mp4'");
            $stmt=$q->execute([
            ]);
            $result = $q->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt){
                throw new Exception($q->errorInfo());
            }
            if($result!= null){
                return $result;
            }else{
                throw new Exception('Aucun resultat');
            }
        }catch(Exception $e){
            return $e;
        }
    }
    public function selectAllMediaName()
    {
        $q = $this->_em->getConnection()->prepare('SELECT `filename`, `type`,`id` FROM media');
        $stmt=$q->execute();
        $result = $q->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRecentDate($id_pro, $grprix_data_id) {
//        dump($id_pro);
//        dump($grprix_data_id);
        $sql = "
            SELECT MAX(date)
            FROM qui_groupe_prix_valeurs
            WHERE id_produit = ?
            AND id_groupe_prix = ?
            ";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id_pro, $grprix_data_id));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getPriceCategory($cat_id) {
        $sql = '
                SELECT CAT_menu, CAT_solo, CAT_petite, CAT_moyenne, CAT_grande, CAT_sans_giant, CAT_avec_giant
                FROM qui_categorie
                FROM qui_categorie
                WHERE CAT_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($cat_id));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        //d($result);
        return $result;
    }
    public function getLabel($cat_nom) {

        $sql = '
                SELECT label_prix_1, label_prix_2, label_prix_3, label_prix_4, label_prix_5, label_prix_6, label_prix_7
                FROM qui_categorie
                FROM qui_categorie
                WHERE CAT_nom = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($cat_nom));
        $result = $query->fetch(PDO::FETCH_NUM);
        return $result;
    }
    public function getGroupePrixID() {
        $sql = '
	            SELECT ID
	            FROM qui_groupe_prix
	            LIMIT 1
	            ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_NUM);
        return $result[0];
    }
    public function getGroupePrix() {
        $sql = '
	            SELECT ID, alias_groupe_prix
	            FROM qui_groupe_prix
	            ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getGroupePrixValeur($critere,$prod_id,$dateID,$grprix_id) {
//	    echo "critere";
//	    dump($critere);
//	    echo "prod_id";
//	    dump($prod_id);
//        echo "dateID";
//	    dump($dateID);
//	    echo "grprix_id";
//	    dump($grprix_id);
        $sql = "
	            SELECT $critere
	            FROM qui_groupe_prix_valeurs
	            WHERE id_produit = ?
	            AND id_groupe_prix = ?
	            AND date = ?
	            ";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($prod_id,$grprix_id,$dateID));
        $result = $query->fetch(PDO::FETCH_NUM);
//        dump($result);
//        d($result);
        return $result;
    }
    public function getProID ($prodName,$catID) {
//	    d($prodName);
//	    d($catID);
        $sql = '
	            SELECT PRO_id
	            FROM qui_produit
	            WHERE PRO_nom = ?
	            AND PRO_CAT_id = ?
	           ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($prodName,$catID));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
//    public function insert($media)
//    {
//        $properties = get_object_vars($media);
//        $values = array_values($properties);
//        $fields = implode("','", $values);
//        $fields = substr($fields, 2) . "'";
//        $sql = '
//                INSERT INTO ' . $this->base . '.media
//                VALUES (NULL, ' . $fields . ')';
//        $query = $this->_em->getConnection()->prepare($sql);
//        $query->execute();
//    }
    function insert($dataObject,$forceId=false)
    {

        $this->_em->persist($dataObject);
        $this->_em->flush();
        return $dataObject->getId();

        /*$methods = get_class_methods($dataObject);
        //conversion camel case en snake case
        $table= strtolower(preg_replace('/(?<=[a-z])[A-Z]/','_$0',get_class($dataObject)));
        $sqlRequestPropertiesWhereInsert = 'INSERT INTO '.$table.'(';
        $sqlRequestValuesToInsert = 'VALUES (';
        $methodsNumber = count($methods);
        foreach ($methods as $index=>$method) {
            if (substr($method, 0, 3) === 'get' && ($method !=='getId' || ($dataObject->getId() && $forceId && $forceId > 0) ) ) {
                $property = substr($method, 3 );
                //si la table est template contents la strategie de nomage utilisé est snake case (par default symfony) donc on converti la propriete en snake case
                if($table === 'template_contents')$property = strtolower(preg_replace('/(?<=[a-z])[A-Z]/','_$0',$property));
                $sqlRequestPropertiesWhereInsert.=$property.',' ;
                $sqlRequestValuesToInsert.= '\''.$dataObject->$method() . '\',';
            }
        }
        $sqlRequestValuesToInsert=substr($sqlRequestValuesToInsert, -1) ===','?substr($sqlRequestValuesToInsert,0,-1).')': $sqlRequestValuesToInsert .')';
        $sqlRequestPropertiesWhereInsert=substr($sqlRequestPropertiesWhereInsert, -1) ===','?substr($sqlRequestPropertiesWhereInsert,0,-1).')': $sqlRequestPropertiesWhereInsert .')';
        $sql = $sqlRequestPropertiesWhereInsert . ' ' . $sqlRequestValuesToInsert ;
        dd($sql);
        $query = $this->_em->getConnection()->prepare($sql);
        $result = $query->execute();
        //$insertedId =  $this->_em->lastInsertId();
        $query = $this->_em->getConnection()->prepare("SELECT");
        $result = $query->execute();
        $insertedId =  $this->_em->getConnection()->lastInsertId();
        if(!$insertedId && $forceId)$insertedId = $forceId;
        return $result ? $insertedId : false;*/
    }
    function update($dataObject){
        $methods = get_class_methods($dataObject);
        $sqlColumns = [];
        foreach ($methods as $method) {
            if (substr($method, 0, 3) === 'get') {
                if($dataObject->$method()!==null){
                    $columnName=substr($method, 3);
                    $sqlColumns[$columnName] = $dataObject->$method();
                }
            }
        }
        $sql = 'UPDATE '.get_class($dataObject).' SET ';
        foreach($sqlColumns as $key=>$value){
            $key=strtolower($key);
            if($key==='id'||$key==='media'){
                $whereCondition = ' WHERE '.$key.'=\''.$value.'\'';
            }else {
                $sql .= ''.$key . '=\'' . $value . '\',';
            }
        }
        if(substr($sql, -1)===','){
            $sql=substr($sql,0,-1);
        }
        if(isset($whereCondition)) {
            $sql.=$whereCondition;
            $query = $this->_em->getConnection()->prepare($sql);
            $query->execute();
        }
    }

    //    public function insertIMG($img)
//    {
//        $properties = get_object_vars($img);
//        $values = array_values($properties);
//        $fields = implode("','", $values);
//        $fields = "'" . $fields . "'";
//        $sql = '
//                INSERT INTO ' . $this->base . '.image
//                VALUES (' . $fields . ')';
//        $query = $this->_em->getConnection()->prepare($sql);
//        $query->execute();
//    }
//    function insertIMG($dataObject)
//    {
////        if(!$table){
////            $table=$this->base.'.'.$type;
////        }
//        $methods = get_class_methods($dataObject);
//        $attributes = [];
//        foreach ($methods as $method) {
//            if (substr($method, 0, 3) === 'get') {
//                $attributes[] = $dataObject->$method();
//            }
//        }
//        $fields = implode("','", $attributes);
//        $fields = "'" . $fields . "'";
////        $fields = substr($fields, 0) . "'";
//        $sql = 'INSERT INTO '.$this->base.'.image VALUES (' . $fields . ')';
//        $query = $this->_em->getConnection()->prepare($sql);
//        $query->execute();
//    }
    public function insertVID($video)
    {
        $properties = get_object_vars($video); // test get_class_vars
        $values = array_values($properties);
        $fields = implode("','", $values);
        $fields = "'" . $fields . "'";
        $sql = '
                INSERT INTO video
                VALUES (' . $fields . ')';
        $query = $this->_em->getConnection()->prepare($sql);
        $result = $query->execute();
        if ($result) {
            return 'ligne insérée avec succès!';
        } else {
            return $fields;
            //return $this->_em->errorInfo();
        }
    }
    public function findAllCategories()
    {
        $sql = '
                    SELECT CAT_id, CAT_nom
                    FROM qui_categorie ORDER BY CAT_nom
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result;
    }
    public function findProducts($categorie)
    {
        $sql = '
                    SELECT qui_produit.PRO_id, qui_produit.PRO_nom, qui_produit.PRO_desc, qui_produit.PRO_nom_usuel
                    FROM qui_produit
                    WHERE qui_produit.PRO_CAT_id = ? ORDER BY `PRO_nom`
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($categorie));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result;
    }
    public function ProductGetName($id)
    {
        $sql = '
                    SELECT PRO_nom
                    FROM qui_produit
                    WHERE qui_produit.PRO_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id));
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result[0];
    }
    public function CategorieGetName($id)
    {
        $sql = '
                    SELECT CAT_nom
                    FROM qui_categorie
                    WHERE CAT_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id));
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result[0];
    }
    public function ProductGetValidInfo($id)
    {
        $sql = '
                    SELECT PRO_menu, PRO_solo, PRO_petite, PRO_moyenne, PRO_grande, PRO_sans_giant, PRO_avec_giant
                    FROM qui_produit
                    WHERE PRO_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    public function getLastId()
    {
        return $this->_em->getConnection()->lastInsertId();
    }
    public function getAssociatedMedia()
    {
        $sql = '
                    SELECT DISTINCT media
                    FROM partnership
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }
    public function updateHigh($mediaId)
    {
        $sql = 'UPDATE media
                   SET high = 1
                   WHERE id = ?
                ';
        //$query = $this->_em->getConnection()->prepare($sql);
        //$query->execute(array($mediaId));
    }
    public function getProductsPriceBoard($resto) {
        $sql = '
                    SELECT PRE_PRO_id, PRE_desc, PRE_rupture
                    FROM qui_produit_resto
                    FROM qui_produit_resto
                    WHERE PRE_RES_id = ? AND (PRE_priceboard_j = 1 OR PRE_priceboard_n = 1)
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($resto));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result;
    }
    public function getCategorie($product)
    {
        $sql = '
                    SELECT PRO_CAT_id, PRO_solo, PRO_menu, PRO_petite, PRO_moyenne
                    FROM qui_produit
                    FROM qui_produit
                    WHERE PRO_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($product));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result[0];
    }
    public function getInfoProduct($product)
    {
        $sql = '
                    SELECT PRO_CAT_id, PRO_solo, PRO_menu, PRO_petite, PRO_moyenne, PRO_grande, PRO_sans_giant, PRO_avec_giant
                    FROM qui_produit
                    FROM qui_produit
                    WHERE PRO_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($product));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result[0];
    }
    public function getPricesold($resto, $product) {
        $sql = '
                    SELECT PRI_champs, PRI_jour, PRI_nuit, PRI_national
                    FROM qui_prix
                    FROM qui_prix
                    WHERE PRI_RES_id = ? AND PRI_PRO_id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($resto, $product));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        return $result;
    }
    public function getPrices($resto, $product) {
        $date_actuelle = $this->getCurrentDate();

        $sql = '
                    SELECT PRI_champs, PRI_jour, PRI_nuit, PRI_national
                    FROM qui_prix
                    FROM qui_prix
                    WHERE PRI_RES_id = ? AND PRI_PRO_id = ? AND PRI_date = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($resto, $product, $date_actuelle));
        $result = $query->fetchAll(PDO::FETCH_NUM);
        if( $result[0][3] == 1 ){ //gestion groupe test sur PRI_national
            $result=array();
            $sql_groupe = '
		            SELECT RES_grpprix
                    FROM qui_restaurant
                    FROM qui_restaurant
                    WHERE RES_id = ?
                ';
            $query_groupe = $this->_em->getConnection()->prepare($sql_groupe);
            $query_groupe->execute(array($resto));
            $groupe_prix = $query_groupe->fetchColumn();
            $tab_types = array("PRO_menu","PRO_solo","PRO_petite","PRO_moyenne","PRO_grande",
                "PRO_sans_giant","PRO_avec_giant",);
            $indice=0;
            foreach ($tab_types as $type) {

                $sql_gr_prix = '
						SELECT '.$type.'_jour,'.$type.'_nuit
						FROM qui_groupe_prix_valeurs
						FROM qui_groupe_prix_valeurs
						WHERE id_groupe_prix = :groupe_prix AND id_produit = :productId AND date = :date_actuelle
					';
                $query = $this->_em->getConnection()->prepare($sql_gr_prix);
                $query->execute(array('groupe_prix' =>$groupe_prix, 'productId' => $product, 'date_actuelle' => $date_actuelle));
                $resultsql = $query->fetchAll(PDO::FETCH_NUM);
                $result[] = (array(0=>$type, 1=>$resultsql[0][0], 2=>$resultsql[0][1], 3=>1));
                $indice++;
            }
        }
        return $result;
    }
    public function getCurrentDate() {
        $sql = '
                    SELECT DISTINCT PRO_date
                    FROM qui_produit
                    FROM qui_produit
                ';

        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchColumn();
        // dump($product); exit();
        return $result;
    }
    public function resolution_verify($media) {
        $sql = '
                    SELECT filetype
                    FROM media
                    WHERE id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($media));
        $type = $query->fetchColumn();
        $sql = '
                    SELECT ratio, height, width
                    FROM ' . $type . '
                    WHERE media = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($media));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getFileName($id)
    {
        $sql = '
                    SELECT filename
                    FROM media
                    WHERE id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array($id));
        $result = $query->fetchColumn();
        return $result;
    }
    public function getFilenameSync($id) {
        $sql = 'SELECT filename FROM admin.video_thematic WHERE id = :id';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(["id" => $id]);
        $result = $query->fetchColumn();
        return $result;
    }
    public function transformMedia($media){
        $exp = explode('.', $media);
        $final = $this->getFileName($exp[0]);
        return $final;
    }
    public function eraseMedia($id, $type) {
        $sql = '
                    DELETE FROM media
                    WHERE id = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $result1 = $query->execute(array($id));
        $sql = '
                    DELETE FROM ' . $type . '
                    WHERE media = ?
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $result2 = $query->execute(array($id));
        if ($result1 && $result2) {
            return true;
        }
        else {
            return false;
        }
    }
    public function getLastInsertions($number) {
        $sql = "
                    SELECT id
                    FROM media
                    ORDER BY id DESC LIMIT $number
                ";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        sort($result, SORT_NUMERIC);
        return $result;
    }
    public function getVideoOrientation($media) {
        $sql = "
                    SELECT height, width
                    FROM video
                    WHERE media = :md
                ";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['md' => $media]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $ratio = $result['height'] / $result['width'];
        if ($ratio < 1) {
            $orientation = 'horizontal';
        } else {
            $orientation = 'vertical';
        }
        return $orientation;
    }
    public function removeUpload($id) {


        $sql= "DELETE FROM template_contents WHERE id = :id";
        $sql= "DELETE FROM template_contents WHERE id = :id";

        $query = $this->_em->getConnection()->prepare($sql);

        $result=$query->execute(array('id' => $id));
        // dump($result);

        $sql = "DELETE FROM media WHERE id = :id";
        $sql = "DELETE FROM media WHERE id = :id";
        $query = $this->_em->getConnection()->prepare($sql);
        $result = $query->execute(array('id' => $id));
        // dump($result);
    }
    public function getProgByPlaylist($playlist) {
        $sql = "SELECT * FROM programmation INNER JOIN main_schedule ON main_schedule.id = programmation.schedule WHERE playlist = :playlist";
        $sql = "SELECT * FROM programmation INNER JOIN main_schedule ON main_schedule.id = programmation.schedule WHERE playlist = :playlist";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['playlist' => $playlist]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $today = new DateTime();
        foreach($result as $i => $prog) {
            $date_prog_end = new dateTime($prog['date_end']);
            if($date_prog_end < $today) {
                $this->deleteProg($prog['id']);
                unset($result[$i]);
            }
        }
        return $result;
    }
    public function is_localProgrammed($media){
        $sql='SELECT   qui_restaurant.RES_numero AS name, local_schedule.site AS id FROM local_schedule
              INNER JOIN qui_restaurant ON local_schedule.site = qui_restaurant.RES_id
              WHERE local_schedule.content_val =?
              GROUP BY local_schedule.site
              ORDER BY local_schedule.site;
        ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->bindParam(1, $media);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function is_programmed($media) {
        $array = [];
        // On récupère toutes les playlists qui contiennent le média en question
        $result = ['playlists' => '', 'schedules' => ''];
        $sql = "
                    SELECT playlist, name
                    FROM screen_playlist
                    INNER JOIN screen_content ON screen_content.screen = screen_playlist.id
                    INNER JOIN playlists ON screen_playlist.playlist = playlists.id
                    FROM screen_playlist
                    INNER JOIN screen_content ON screen_content.screen = screen_playlist.id
                    INNER JOIN playlists ON screen_playlist.playlist = playlists.id
                    WHERE content = :media
                ";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['media' => $media]);
        $boucles = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($boucles as $boucle) {
            $tmp = [];
            $tmp["name"] = $boucle["name"];
            $tmp["id"] = $boucle["playlist"];
            $tmp["programmation"] = $this->getProgByPlaylist($boucle["playlist"]);
            $array[] = $tmp;
        }
        //dump($array);
        // $playlist_ids = [];
        // if(count($boucles) > 1) {
        //     $result['playlists'] = '<p>Le média que vous souhaitez supprimer est actuellement présent dans les playlists suivantes : </p><ul>';
        // } else {
        //     $result['playlists'] = '<p>Le média que vous souhaitez supprimer est actuellement présent dans la playlist suivante : </p><ul>';
        // }
        // foreach ($boucles as $i => $boucle) {
        //     $result['playlists'] .= '<li>' . $boucle['name'] . '</li>';
        //     $playlist_ids[] = $boucle['playlist'];
        //     // if(count($boucles) > 1 && $i != count($boucles)-1) {
        //     //     $result['playlists'] .= ' & "';
        //     // }
        // }
        // $result['playlists'] .= '</ul> ';
        // // On recherche toutes les programmations des playlists retenues
        // $array_fragment = '(' . implode(',', $playlist_ids) . ')';
        // $sql = "SELECT schedule, denomination, room FROM $this->base.programmation INNER JOIN main_schedule ON programmation.schedule = main_schedule.id WHERE playlist IN " . $array_fragment . "";
        // $query = $this->_em->getConnection()->prepare($sql);
        // $query->execute(['today' => $today]);
        // $schedules = $query->fetchAll(\PDO::FETCH_ASSOC);
        // $nbr_centrales = count($schedules);
        // if ($nbr_centrales > 0) {
        //     if(count($boucles) > 1) {
        //         $result['schedules'] = '<p>Ces playlists sont diffusées par ';
        //     } else {
        //         $result['schedules'] = '<p>Cette playlist est diffusée par ';
        //     }
        //     if($nbr_centrales > 1) {
        //         $result['schedules'] .= 'les groupes suivants : </p>';
        //     } else {
        //         $result['schedules'] .= 'le groupe suivant : </p>';
        //     }
        //     foreach ($schedules as $i => $schedule) {
        //         $result['schedules'] .= $schedule['denomination'] . '<a href="resume_schedule_3.php?schedule='.$schedule["schedule"].'&day='.date('Y-m-d').'&room='.$schedule["room"].'" title="Voir la programmation" target="_blank">Voir</a>';
        //         if($nbr_centrales > 1 && $i != $nbr_centrales-1) {
        //             $result['schedules'] .= ' - "';
        //         }
        //     }
        //     $result['schedules'] .= '</p>';
        // }
        // else {
        //     $result['schedules'] = null;
        // }
        // if($boucles == []) {
        //     return 0;
        // }
        // else {
        //     return $result['playlists'] /*. $result['schedules']*/;
        // }
        return $array;
    }
    public function getTypeMedia($mediaId){
        $type = null;
        $sql = "SELECT `extension` FROM `image` WHERE `media` = :media";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(array('media' => $mediaId));
        $testImage = $query->fetch(PDO::FETCH_COLUMN);
        if( !$testImage){
            $type = 'video';
        }
        else{
            $type = 'image';
        }
        return $type;
    }
    public function getAvailableTypesPrice($product) {

        $sql = '
                SELECT PRO_menu, PRO_solo, PRO_petite, PRO_moyenne, PRO_grande, PRO_sans_giant, PRO_avec_giant
                FROM qui_produit
                FROM qui_produit
                WHERE PRO_id = :prd
                ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['prd' => $product]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    private function deleteProg($id) {
        $sql = "DELETE FROM programmation WHERE id = :which";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['which' => $id]);
    }

    private function getMediaInfos(int $mediaId)
    {

        $media = $this->find($mediaId);

        if(!$media)
            throw new Exception(sprintf("No media can be found with this id : '%s'", $mediaId));

        $infos = [
            'media_diff_start' => $media->getDiffusionStart()->format('d/m/Y'),
            'media_diff_end' => $media->getDiffusionEnd()->format('d/m/Y'),
            'media_incrustations' => [],
            'media_products' => [],
            'media_criterions' => [],
            'media_tags' => [],
            'media_allergens'=> [],
            'diffusionSpaces'=> []
        ];

        foreach ($media->getTags()->getValues() as $tag)
        {
            if(!in_array($tag->getName(), $infos['media_tags']))
                $infos['media_tags'][] = $tag->getName();
        }

        foreach ($media->getProducts()->getValues() as $product)
        {

            $infos['media_products'][] = $product->getName();


            foreach ($product->getIncrustes()->getValues() as $incruste)
            {
                $infos['media_incrustations'][$product->getName()][] = $incruste->getTypeIncruste();
            }


            foreach ($product->getCriterions()->getValues() as $criterion)
            {
                if(!in_array($criterion->getName(), $infos['media_criterions']))
                    $infos['media_criterions'][] = $criterion->getName();
            }

            foreach ($product->getTags()->getValues() as $tag)
            {
                if(!in_array($tag->getName(), $infos['media_tags']))
                    $infos['media_tags'][] = $tag->getName();
            }

            foreach ($product->getAllergens()->getValues() as $allergen)
            {
                $allergenId = $allergen->getAllergenId();
                $allergen = $this->allergenRepository->find($allergenId);
                if(!$allergen)
                    throw new Exception(sprintf("No allergen found with id : '%s'", $allergenId));

                if(!in_array($allergen->getName(), $infos['media_allergens']))
                    $infos['media_allergens'][] = $allergen->getName();
            }

        }

        //dd($infos);

        return $infos;

    }
    private function paginate(string $query, int $page = 1, int $limit = 15)
    {

        $query = $this->_em->createQuery($query)
                           ->setFirstResult($limit * ($page - 1))
                           ->setMaxResults($limit);

        return ( new Paginator($query, $fetchJoinCollection = true))->setUseOutputWalkers(false);

    }

}