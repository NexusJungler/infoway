<?php


namespace App\Service;


use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\FfmpegTasks;
use App\Repository\Admin\FfmpegTasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use \Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class FfmpegSchedule
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FfmpegTasksRepository
     */
    private $ffmpegRepo;

    /**
     * @var array
     */
    private array $customers;

    /**
     * @var array
     */
    private array $conf;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, LoggerInterface $cronLogger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $cronLogger;
        $this->ffmpegRepo = $entityManager->getRepository(FfmpegTasks::class);
        $config_file = file_get_contents($parameterBag->get('configDirectory') . 'sys_flags.yml');
        $this->conf = Yaml::parse($config_file);
        $this->customers = $this->getDataSheetClients();
    }


    /**
     * Register a new ffmpeg task in db
     *
     * @param Customer $customer
     * @param string $fileName
     * @param string $fileType
     * @param string $mediaType
     * @throws \Exception
     */
    public function pushTask(Customer $customer, string $fileName, string $fileType, string $mediaType)
    {

        if(strlen($mediaType) > 4)
            throw new \Exception(sprintf("Error ! mediaType maxlength is 4 but argument given length is over than it !"));

        $ffmpeg_task = new FfmpegTasks();
        $ffmpeg_task->setFilename($fileName)
                    ->setFiletype($fileType)
                    ->setMediatype($mediaType)
                    ->setRegistered(new \DateTime())
                    //->setCustomer($customer)
        ;

        $customer->addUploadTask($ffmpeg_task);

        $this->entityManager->persist($ffmpeg_task);
        $this->entityManager->flush();

    }

    public function runTasks()
    {

        if(!$this->taskIsRunning())
        {

            $this->logger->error(sprintf("Log[%s] -- %s : A new Ffmpeg task start now !", __CLASS__, date('d/m/Y - G:i:s')));

            // Au début du traitement, mise à jour du fichier de configuration: FFMPEG -> 1
            $this->conf['ffmpeg'] = 1;
            file_put_contents(__DIR__ . '/../config/sys_flags.yml', Yaml::dump($this->conf));

            // On récupère toutes les tâches de traitement en attente et on les exécute l'une après l'autre
            $tasks = $this->getTasks();

            foreach ($tasks as $task)
            {
                $customer_id = $task->getCustomer()->getId();
                // dump($task);
                $base = $this->customers[$customer_id]['base'];
                $customer_name = $this->customers[$customer_id]['enseigne'];

                if($task['mediatype'] == 'sync') {
                    $rep_sync = new synchro_rep($base);
                    $path = 'D:/node_file_system/' . $customer_name . '/synchros/' . $task['filename'];
                    $temp_folder = 'C:/inetpub/wwwroot/admin/node_JS/node_ftp_server/temp';

                    if (file_exists($path)) {
                        $real_file_extension = $this->getRealFileExtension($path);
                        if($real_file_extension != 'zip' && $real_file_extension != 'rar') {
                            $this->updateTask($task, 'started');
                            $this->pushError($task, 'permission denied - ' .  $real_file_extension . ' forbidden extension');
                            $this->updateTask($task, 'finished');
                            continue;
                        }
                    } else {
                        $this->updateTask($task, 'started');
                        $this->pushError($task, 'file not found!');
                        $this->updateTask($task, 'finished');
                        continue;
                    }

                    exec('7z e "' . $path . '" -o' . $temp_folder, $output);
                    exec('rmdir "' . $temp_folder . '/' . substr($task->getFilename(), 0, -4) . '"');

                    $this->updateTask($task, 'started');
                    $list = scandir($temp_folder);
                    $sorted_medias = [];
                    unset($list[0], $list[1]);
                    $all_sync_errors = [];
                    $encoding = null;
                    foreach($list as $video) {
                        if(is_dir($video)) {
                            $this->deldir($video);
                            continue;
                        }

                        $encoding = new EncodageHandler($customer_name, $video, 'sync');
                        foreach($encoding->getErrors() as $error) {
                            $all_sync_errors[] = $error;
                        }
                        $sorted_medias[] = $encoding->fileID;
                    }
                    $error_string = implode(' || ', $all_sync_errors);
                    if($error_string != '') {
                        $this->pushError($task['id'], $error_string);
                    }
                    // Add new entity synchro & Erase uploaded zip file
                    $new_sync = $rep_sync->insertSynchro(substr($task['filename'], 0, -4), count($list), $encoding->getSyncOrientation(), 'plein-écran');
                    $rep_sync->saveSyncMedias($new_sync, $sorted_medias);
                    unlink($path);
                    $this->updateTask($task['id'], 'finished');
                } else {
                    $this->updateTask($task['id'], 'started');
                    $encoding = new UploadCron($customer_name, $task['filename'], $task['mediatype']);
                    $errors = $encoding->getErrors();
                    $error_string = implode(' || ', $errors);
                    if($error_string != '') {
                        $this->pushError($task['id'], $error_string);
                    }
                    $this->updateTask($task['id'], 'finished');
                    // $this->killTask($task['id']);
                }
            }

            // A la fin du traitement, mise à jour du fichier de configuration: FFMPEG -> 0
            $this->conf['ffmpeg'] = 0;
            file_put_contents(__DIR__ . '/../config/sys_flags.yml', Yaml::dump($this->conf));
            file_put_contents(__DIR__ . '/../log/ffmpeg.log', date('Y-m-d H:i:s') . ' --> ' . count($tasks) . ' nouveaux médias téléchargés via protocole FTP ont été réencodés' . PHP_EOL, FILE_APPEND);

            $this->logger->error(sprintf("Log[%s] -- %s : A Ffmpeg task is finish !", __CLASS__, date('d/m/Y - G:i:s')));

        }

        else
            $this->logger->error(sprintf("Log[%s] -- %s : An Ffmpeg task is already running !", __CLASS__, date('d/m/Y - G:i:s')));

    }

    /**
     * Remove an ffmpeg task from db
     *
     * @param FfmpegTasks $task
     */
    public function removeTask(FfmpegTasks $task)
    {

        //$task = $this->ffmpegRepo->findOneById($id);

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function updateTask(FfmpegTasks $task, string $fieldToUpdate)
    {

        /*$task = $this->ffmpegRepo->findOneById($id);

        if(!$task)
            throw new Exception(sprintf("Internal Error: Cannot found FfmpegTasks instance with id '%d'", $id));

        elseif(!property_exists($task, $fieldToUpdate))
            throw new Exception(sprintf("Internal Error: '%s' doesn't have '%s' property !", FfmpegTasks::class, $fieldToUpdate));

        elseif(!method_exists($task, 'set' . ucfirst($fieldToUpdate)))
            throw new Exception(sprintf("Internal Error: '%s' doesn't have '%s' method !", FfmpegTasks::class, 'set' . ucfirst($fieldToUpdate)));

        else
            call_user_func_array([$task, 'set' . ucfirst($fieldToUpdate)], [new \DateTime()]);*/

        // on peut directement utiliser les setters (setRegistered, setStarted, setFinished)
        // mais de cette façon le code est dynamique
        // on  peut ajouter/modifier la proprité à modifier, le code restera fonctionnel
        // sinon a chaque ajout d'une propriété il faudra revenir modifier cette fonction pour rajouter l'appel du setter correspondant
        if(!property_exists($task, $fieldToUpdate))
            throw new Exception(sprintf("Internal Error: '%s' don't have '%s' property !", FfmpegTasks::class, $fieldToUpdate));

        elseif(!method_exists($task, 'set' . ucfirst($fieldToUpdate)))
            throw new Exception(sprintf("Internal Error: '%s' don't have '%s' method !", FfmpegTasks::class, 'set' . ucfirst($fieldToUpdate)));

        else
            call_user_func_array([$task, 'set' . ucfirst($fieldToUpdate)], [new \DateTime()]);

        $this->entityManager->flush();

    }

    private function getDataSheetClients()
    {
        /*$result = [];
        $sql = '
                    SELECT *
                    FROM enseignes
                ';
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $clients = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($clients as $client) {
            $key = $client['id'];
            unset($client['id']);
            $result[$key] = $client;
        }
        return $result;*/
        return [];
    }

    private function taskIsRunning()
    {
        return $this->conf['ffmpeg'] == 1;
    }

    private function pushError(FfmpegTasks $task, string $error)
    {

        /*$task = $this->ffmpegRepo->findOneById($id);

        if(!$task)
            throw new Exception(sprintf("Internal Error: Cannot found FfmpegTasks instance with id '%d'", $id));*/

        $task->setErrors($error);

        $this->entityManager->flush();

    }

    /**
     * Return all tasks which is not finished order by ASC
     *
     * @return FfmpegTasks[] | array
     */
    private function getTasks()
    {

        return $this->ffmpegRepo->createQueryBuilder('f')
                                ->andWhere('f.started != null')
                                ->andWhere('f.finished = null')
                                ->orderBy('f.registered', 'ASC')
                                ->getQuery()
                                ->getArrayResult();

    }

    public function getCustomerId($customer) {
        /*$array = [];
        foreach ($this->customers as $id => $client) {
            $array[$client['enseigne']] = $id;
        }
        return $array[$customer];*/
    }

    private function getRealFileExtension($path) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        $splash = explode('/', $mimeType);
        return $splash[1];
    }

    private function deldir($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? deldir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}