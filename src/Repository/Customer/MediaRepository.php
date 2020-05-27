<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\Video;
use App\Repository\MainRepository;
use App\Repository\RepositoryInterface;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PDO;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{

    use MainRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * Renvoie un tableau contenant tout les médias qui doivent contenir des incrustes mais qui ne contiennent pas encore d'incruste
     *
     * @return array
     */
    public function getMediasInWaitingListForIncrustes(): array
    {

        $mediasInWaitingList = [];

        $medias = $this->createQueryBuilder('m')
                                   ->leftJoin(Image::class,"i", "WITH", "i.containIncruste = true AND i.incrustes IS EMPTY")
                                   ->leftJoin(Video::class,"v", "WITH", "v.containIncruste = true AND v.incrustes IS EMPTY")
                                   ->getQuery()
                                   ->getResult();

        $mediasInWaitingList['number'] = sizeof($medias);
        $mediasInWaitingList['medias'] = $medias;

        return $mediasInWaitingList;

    }


    public function getMediaByType(string $type)
    {

        $typeOfMediasToSearch = [];
        $medias = [];

        switch ($type)
        {

            case "medias":
                //$typeOfMediasToSearch['types']= [ 'image', 'video' ];

                $medias = $this->createQueryBuilder('m')
                                ->leftJoin(Image::class,"i", "WITH", "(i.containIncruste = false) OR (i.containIncruste = true AND i.incrustes IS NOT EMPTY)")
                                ->leftJoin(Video::class,"v", "WITH", "(v.containIncruste = false) OR (v.containIncruste = true AND v.incrustes IS NOT EMPTY)")
                                ->distinct()
                                ->getQuery()
                                ->getResult();

                break;

            case "video_synchro":
                $typeOfMediasToSearch['types']= [ 'sync' ];
                break;

            case "video_thematic":
                $typeOfMediasToSearch['types']= [ 'them' ];
                break;

            case "element_graphic":
                $typeOfMediasToSearch['types']= [ 'elgp' ];
                break;

            default:
                throw new Exception(sprintf("Error : Unrecognized media type ! Trying to get medias with '%s' type but this media type is not exist ", $type));

        }

        return $medias;

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
        $row = $query->fetch(\PDO::FETCH_NUM);
        $result = $row[0];
        return $result;
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
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
        $result = $query->fetch(\PDO::FETCH_NUM);
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
        $result = $query->fetch(\PDO::FETCH_NUM);
        return $result[0];
    }
    public function getGroupePrix() {
        $sql = '
	            SELECT ID, alias_groupe_prix
	            FROM qui_groupe_prix
	            ';
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
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
        $result = $query->fetch(\PDO::FETCH_NUM);
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_COLUMN);
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
        $result = $query->fetchAll(\PDO::FETCH_COLUMN);
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
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
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
        $result = $query->fetchAll(\PDO::FETCH_COLUMN);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetchAll(\PDO::FETCH_NUM);
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
                $resultsql = $query->fetchAll(\PDO::FETCH_NUM);
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
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
        $result = $query->fetchAll(\PDO::FETCH_COLUMN);
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
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
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
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
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
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
        $boucles = $query->fetchAll(\PDO::FETCH_ASSOC);
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
        $testImage = $query->fetch(\PDO::FETCH_COLUMN);
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
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }
    private function deleteProg($id) {
        $sql = "DELETE FROM programmation WHERE id = :which";
        $query = $this->_em->getConnection()->prepare($sql);
        $query->execute(['which' => $id]);
    }


    // /**
    //  * @return Media[] Returns an array of Media objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Media
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
