<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Synchro;
use App\Repository\MainRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectManager;

/**
 * @method Synchro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Synchro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Synchro[]    findAll()
 * @method Synchro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynchroRepository extends ServiceEntityRepository
{

    private string $base;

    use MainRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Synchro::class);
        $singleton = false;
        if($this->base != null) {
            $singleton = true;
        }
    }

    /**
     * @param array $data
     * @return false|string
     */
    public function JsonStringify(array $data)
    {
        $string = "'[";
        foreach ($data as $value) {
            $string .="\"" . $value . "\"," ;
        }
        $string = substr($string, 0, -1);
        $string .= "]'";
        return $string;
    }


    /**
     * @param string $name
     * @param int $amount_screens
     * @param string $orientation
     * @param string $format
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insertSynchro(string $name, int $amount_screens, string $orientation, string $format): int
    {

        $synchro = new Synchro();
        $synchro->setDirectory($name)
                ->setNbrFiles($amount_screens)
                ->setOrientation($orientation)
                ->setFormat($format);

        $this->_em->persist($synchro);
        $this->_em->flush();

        // return last insert id
        return $synchro->getId();

    }

    /**
     * @param int $id
     * @return string|null
     */
    public function getSyncName(int $id): ?string
    {
        $sync = $this->findOneById($id);

        return ($sync) ? $sync->getDirectory() : null;
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function getNbrFiles(int $id): ?int
    {
        $sync = $this->findOneById($id);

        return ($sync) ? $sync->getNbrFiles() : null;
    }

    /**
     * @return string|null
     */
    public function getMaxScreensForAllSync(): ?string
    {

        $maxScreen = $this->createQueryBuilder("s")
                    ->select('MAX(s.nbrFiles)')
                    ->getQuery()
                    ->getArrayResult();

        return (!empty($maxScreen)) ? $maxScreen[0][1] : null;

    }

    /**
     * @param int $id
     * @return array
     */
    public function getSynchroByPlaylist(int $id): array
    {

        return $this->createQueryBuilder("s")
                          ->where("s.id = :id")
                          ->setParameter("id", $id)
                          ->getQuery()
                          ->getArrayResult();

    }

    /**
     * @param string $directory
     * @return array
     */
    public function getSyncData(string $directory): array
    {

        return $this->createQueryBuilder("s")
                         ->where("s.directory = :directory")
                         ->setParameter("directory", $directory)
                         ->getQuery()
                         ->getArrayResult();

    }

    public function getSyncFrequencyByKey(int $id) {
        // dump($id);



        $sql = "
                    SELECT frequence
                    FROM $this->base.synchro_playlist
                    WHERE id = :which
                ";
        $query = $this->connection->prepare($sql);
        $query->execute(['which' => $id]);
        $result = $query->fetchColumn();
        return $result ? $result : 180;
    }


    public function getSyncFrequencyInPlaylistByKey($id) {
        $sql = "
                    SELECT frequence
                    FROM $this->base.synchro_playlist
                    WHERE id = :which
                ";
        $query = $this->connection->prepare($sql);
        $query->execute(['which' => $id]);
        $result = $query->fetchColumn();
        return $result ? $result : 180;
    }

    public function getSyncFrequency($playlist, $synchro) {
        $sql = "
                    SELECT frequence
                    FROM $this->base.synchro_playlist
                    WHERE playlist = :bcl AND synchro = :sync
                ";
        $query = $this->connection->prepare($sql);
        $query->execute(['bcl' => $playlist, 'sync' => $synchro]);
        $result = $query->fetchColumn();
        return $result;
    }

    public function updateFrequency($value, $id) {
        $sql = '
                    UPDATE synchro_playlist
                    SET frequence = :val
                    WHERE id = :which
                ';
        $query = $this->connection->prepare($sql);
        $result = $query->execute(array('val' => $value, 'which' => $id));
        return $result;
    }

    public function getAvailableSync($format, $interface = false) {
        include __DIR__ . '/../config/include_piw.php';
        $root = __DIR__ . '/../' . $_SESSION['QUICKNET']['RES_rep'];
        $dir = $root . '/VIDEOS/SYNC/';
        $scan = scandir($dir);
        $synchros = [];
        foreach ($scan as $i => $directory) {
            if ($i > 1) {
                $synchros[] = $directory;
            }
        }
        $result = [];
        foreach ($synchros as $directory) {
            $files = scandir($dir . $directory);
            foreach ($files as $y => $file) {
                $boom = explode('.', $file);
                $ext = $boom[1];
                if($y > 1 && $ext == 'mp4') {
                    if(!$interface || ($interface && $file != 'intro_sync.mp4')) {
                        $result[$directory][] = $file;
                    }
                }
            }
            if(!$interface) {
                $screen_amount = $format+1;
            } else {
                $screen_amount = $format;
            }
            if(count($result[$directory]) != $screen_amount) {
                unset($result[$directory]);
            }
        }
        return $result;
    }

    /*public function linkSynchro($synchro, $playlist) {
        $sql = 'INSERT INTO ' . $this->base . '.synchro_playlist (playlist, synchro, frequence) VALUES (:bcl, :sync, 120)';
        $query = $this->connection->prepare($sql);
        $query->execute(['bcl' => $playlist, 'sync' => $synchro]);
        $result = $this->connection->lastInsertId();
        return $result;
    }*/

    public function linkSynchro($synchro, $playlist, $frequence = 120) {
        $sql = 'INSERT INTO ' . $this->base . '.synchro_playlist (playlist, synchro, frequence) VALUES (:bcl, :sync, :frequence)';
        $query = $this->connection->prepare($sql);
        $query->execute(['bcl' => $playlist, 'sync' => $synchro, 'frequence' => $frequence]);
        $result = $this->connection->lastInsertId();
        return $result;
    }

    public function deleteLink($id) {
        $sql = 'DELETE FROM ' . $this->base . '.synchro_playlist WHERE id= :which';
        $query = $this->connection->prepare($sql);
        $result = $query->execute(['which' => $id]);
        return $result;
    }

    public function deleteSync($sync){
        // Suppression de la synchronisation dans la table 'synchro'
        $sql = 'DELETE FROM ' . $this->base . '.synchro WHERE id= :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
        // Suppression de la liaison avec la playlist et la synchronisation dans la table 'synchro_playlist'
        $sql = 'DELETE FROM ' . $this->base . ' .synchro_playlist WHERE synchro = :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
        // Suppression des contenus de la synchronisation dans la table 'synchro_content'
        $sql = 'DELETE FROM ' . $this->base . ' .synchro_content WHERE synchro = :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
    }

    public function getInfosBySync($sync){
        $array = [];
        $sql = 'SELECT playlist FROM ' . $this->base . '.synchro_playlist WHERE synchro = :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
        $values = $query->fetchAll(\PDO::FETCH_ASSOC);
        if(count($values) != 0){
            foreach($values as $key => $value){
                $sql = 'SELECT programmation.id, name, denomination, date_end FROM ' . $this->base . '.programmation INNER JOIN ' . $this->base . '.main_schedule ON programmation.schedule = main_schedule.id INNER JOIN ' . $this->base . '.playlists ON playlists.id = programmation.playlist WHERE playlist = :play';
                $query = $this->connection->prepare($sql);
                $query->execute(['play' => $value['playlist']]);
                $progs = $query->fetchAll(\PDO::FETCH_ASSOC);
                //dump($progs);
                foreach($progs as $prog) {
                    $date_end_prog = new DateTime($prog['date_end']);
                    $today = new DateTime();
                    //dump($date_end_prog, $today);
                    if($date_end_prog < $today) {
                        $this->deleteProg($prog['id']);
                    } else {
                        $array[$key] = ['playlist' => $prog['name'], 'schedule' => $prog['denomination']];
                    }
                }
            }
        }
        return $array;
    }

    private function deleteProg($id) {

        $sql = "DELETE FROM programmation WHERE id = :which";
        $query = $this->connection->prepare($sql);
        $query->execute(['which' => $id]);
    }

    public function getLinkedSynchros($playlist) {
        $sql = "
                    SELECT *
                    FROM $this->base.synchro_playlist
                    WHERE playlist = :playlist
                ";
        $query = $this->connection->prepare($sql);
        $query->execute(['playlist' => $playlist]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getSyncDurationByName($name) {
        $root = __DIR__ . '/../' . $_SESSION['QUICKNET']['RES_rep'] . '/VIDEOS/SYNC/';
        $file = $root . $name . '/0.mp4';
        $path_to_ffmpeg = 'C:/inetpub/wwwroot/bin/';
        exec($path_to_ffmpeg . 'ffprobe -v quiet -print_format json -show_format -show_streams "'. $file . '" > "..\tmp\infofile.json"');
        $data = file_get_contents(__DIR__ . '/../../../tmp/infofile.json');
        $json = (array) json_decode($data);
        return round($json['streams'][0]->duration, 2);
    }

    public function getSyncDurationByKey($id) {
        $medias = $this->getFilesListSync($id);
        $sql = "
                    SELECT duration
                    FROM $this->base.video
                    WHERE media = :which
                ";
        $query = $this->connection->prepare($sql);
        $query->execute(['which' => $medias[1]]);
        $result = $query->fetchColumn();
        return floatval(substr($result, 0, -9));
    }

    public function getFilesListSync($sync) {
        $sql = 'SELECT media FROM synchro_content WHERE synchro = :sync ORDER BY position';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    public function getAllSyncByHardware($orientation, $screens_amount = null) {
        $params = ['ori' => $orientation];
        $sql = 'SELECT id FROM synchro WHERE orientation = :ori';
        $sql2 = ' AND nbr_files = :nbr';
        if($screens_amount != null) {
            $sql .= $sql2;
            $params['nbr'] = $screens_amount;
        }
        $query = $this->connection->prepare($sql);
        $query->execute($params);
        $sync_id = $query->fetchAll(PDO::FETCH_COLUMN);
        $data = [];
        foreach ($sync_id as $sync) {
            $data[$sync] = $this->getFilesListSync($sync);
        }
        return $data;
    }

    public function saveSyncMedias($sync, $files) {
        foreach ($files as $pos => $file) {
            $sql = 'INSERT INTO synchro_content (synchro, media, position) VALUES (:sync, :file, :pos)';
            $query = $this->connection->prepare($sql);
            $query->execute(['sync' => $sync, 'file' => $file, 'pos' => $pos]);
        }
    }

    public function updateSyncMedias($sync, $files) {
        $result = [];
        foreach ($files as $pos => $file) {
            $sql = 'UPDATE synchro_content SET position = :ord WHERE synchro = :sync AND media = :med';
            $query = $this->connection->prepare($sql);
            $result[] = $query->execute(['sync' => $sync, 'ord' => $pos, 'med' => $file]);
        }
        return $result;
    }

    public function changeSyncName($id, $newName) {
        $sql = 'UPDATE synchro SET directory = :dry WHERE id = :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['dry' => $newName, 'sync' =>$id]);
    }

    public function getAllSyncNames() {
        $sql = 'SELECT directory FROM synchro';
        $query = $this->connection->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    public function getDataSamsung() {
        $sql = 'SELECT * FROM qui_lfd WHERE RES = :client';
        $query = $this->connection->prepare($sql);
        $query->execute(['client' => 'KFC_SAMSUNG']);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $csvFileName = 'SamsungLFD.csv';
        $fp = fopen($csvFileName, 'w');
        foreach($result as $row){
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /*public function deleteSync($sync){
        $sql = 'DELETE FROM ' . $this->base . '.synchro WHERE id= :sync';
        $query = $this->connection->prepare($sql);
        $query->execute(['sync' => $sync]);
    }*/

    public function addLocalSync($room, $display_space, $site, $slot, $sync, $id) {
        if ($id == NULL) {
            $sql = 'INSERT INTO local_sync (room, display_space, site, day, slot, sync, frequence, is_sync_active) VALUES (:room, :display_space, :site, "2030-01-01", :slot, :sync, 120, 1)';
            $query = $this->connection->prepare($sql);
            $query->execute(['room' => $room, 'display_space' => $display_space, 'site' => $site, 'slot' => $slot, 'sync' => $sync]);
            $last_id = $this->connection->lastInsertId();
        }
        else {
            $sql = 'UPDATE local_sync SET sync = :sync, is_sync_active = 1 WHERE id = :id';
            $query = $this->connection->prepare($sql);
            $query->execute(['sync' => $sync, 'id' => $id]);
            $last_id = $id;
        }
        return $last_id;
    }

    public function hideLocalSync($room, $display_space, $site, $slot, $id) {
        if ($id == NULL) {
            $sql = 'INSERT INTO local_sync (room, display_space, site, day, slot, is_sync_active) VALUES (:room, :display_space, :site, "2030-01-01", :slot, 0)';
            $query = $this->connection->prepare($sql);
            $query->execute(['room' => $room, 'display_space' => $display_space, 'site' => $site, 'slot' => $slot]);
            $last_id = $this->connection->lastInsertId();
        }
        else {
            $sql = 'UPDATE local_sync SET sync = 0, frequence = 0, is_sync_active = 0 WHERE id = :id';
            $query = $this->connection->prepare($sql);
            $query->execute(['id' => $id]);
            $last_id = $id;
        }
        return $last_id;
    }

    public function showLocalSync($id) {
        $sql = 'UPDATE local_sync SET is_sync_active = 1 WHERE id = :id';
        $query = $this->connection->prepare($sql);
        $query->execute(['id' => $id]);
    }

    public function is_sync_diffusion($room, $display_space, $site, $slot) {
        $sql = 'SELECT * FROM local_sync WHERE room = :room AND display_space = :display_space AND site = :site AND slot = :slot';
        $query = $this->connection->prepare($sql);
        $query->execute(['room' => $room, 'display_space' => $display_space, 'site' => $site, 'slot' => $slot]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function editLocalSync($room, $display_space, $site, $slot, $sync, $frequence, $id) {
        if ($id != NULL) {
            $sql = 'UPDATE local_sync SET frequence = :frequence WHERE id = :id';
            $query = $this->connection->prepare($sql);
            $query->execute(['frequence' => $frequence, 'id' => $id]);
        }
        else {
            $sql = 'INSERT INTO local_sync (room, display_space, site, day, slot, sync, frequence, is_sync_active) VALUES (:room, :display_space, :site, "2030-01-01", :slot, :sync, :frequence, 1)';
            $query = $this->connection->prepare($sql);
            $query->execute(['room' => $room, 'display_space' => $display_space, 'site' => $site, 'slot' => $slot, 'sync' => $sync, 'frequence' => $frequence]);
        }
    }

    public function checkSyncLocal($room, $display_space, $site, $slot, $dead_line = null) {
        $sql = 'SELECT * FROM local_sync WHERE room = :room AND display_space = :display_space AND site = :site AND slot = :slot AND day >= :dead_line';
        $query = $this->connection->prepare($sql);
        $query->execute(['room' => $room, 'display_space' => $display_space, 'site' => $site, 'slot' => $slot, 'dead_line' => $dead_line]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function restoreMainSync($id) {
        $sql = 'DELETE FROM local_sync WHERE id = :id';
        $query = $this->connection->prepare($sql);
        $query->execute(['id' => $id]);
    }

    public function cleanTable($source, $target, $field) {
        // On récupére l'ensemble des id de la table source
        $sql = 'SELECT id FROM '.$source.'';
        $query = $this->connection->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN);
        $available = join(",", $result);
        //dump($available);
        // On récupére les id de la table 'cible' qui ne posséde l'un des id de la table 'source'
        $sql = "SELECT id FROM ".$target." WHERE NOT ".$field." IN ($available)";
        $query = $this->connection->prepare($sql);
        $query->execute();
        $result1 = $query->fetchAll(PDO::FETCH_COLUMN);
        //dump($result1);
        if (count($result1) > 0) {
            $reject = join(",", $result1);
            $sql = "DELETE FROM ".$target." WHERE id IN ($reject)";
            $query = $this->connection->prepare($sql);
            $query->execute();
        }
    }

    public function treatmentDisplay() {
        $sql = "SELECT site, room FROM local_schedule RIGHT JOIN room ON room.id = local_schedule.room GROUP BY room, site";
        $query = $this->connection->prepare($sql);
        $query->execute();
        $sites = $query->fetchAll(PDO::FETCH_ASSOC);
        //dump($sites);
        foreach ($sites as $value) {
            $sql = "SELECT id FROM display_space WHERE room = :room AND site = :site";
            $query = $this->connection->prepare($sql);
            $query->execute(array('room' => $value["room"], 'site' => $value["site"]));
            $dsp = $query->fetchAll(PDO::FETCH_COLUMN);
            if (count($dsp) === 1) {
                $sql = "SELECT id FROM local_schedule WHERE room = :room AND site = :site";
                $query = $this->connection->prepare($sql);
                $query->execute(array('room' => $value["room"], 'site' => $value["site"]));
                $results = $query->fetchAll(PDO::FETCH_COLUMN);
                $ids = join(",", $results);
                $sql = "UPDATE local_schedule SET room = :display WHERE id IN ($ids) AND site = :site";
                $query = $this->connection->prepare($sql);
                $query->execute(array('display' => $dsp[0], 'site' => $value["site"]));
            }
        }
    }


    // /**
    //  * @return Synchro[] Returns an array of Synchro objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Synchro
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
