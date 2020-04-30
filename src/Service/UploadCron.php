<?php


namespace App\Service;


use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\Video;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;
use \Doctrine\Persistence\ObjectManager;
use \Doctrine\Persistence\ManagerRegistry;

class UploadCron
{

    public $fileID;
    private $errors = [];
    private $customer;
    private $customer_dir;
    private $filetype;
    private $mediatype;
    private $filename;
    private $extension;
    private $destfolder;
    //private $srcfolder = 'D:/node_file_system/';
    private $srcfolder;
    private $repository;
    private $syncOri;
    private $fileDiffusionStart;
    private $fileDiffusionEnd;

    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * UploadCron constructor.
     *
     * @param $customer_name
     * @param $filename
     * @param $mediatype
     * @param ManagerRegistry $managerRegistry If you give ManagerRegistry (Doctrine), this class can access all app EntityManager
     * @param ParameterBagInterface $parameterBag for access parameter (which is defined in config/services.yaml)
     */
    public function __construct(array $taskInfo, ManagerRegistry $managerRegistry, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->customer = $taskInfo['customerName'];
        $this->customer_dir = $this->getCustomerDirectory($this->customer);
        $this->mediatype = $taskInfo['mediaType'];
        $this->fileUploadDate = $taskInfo['uploadDate'];

        $this->fileDiffusionStart = new DateTime();

        $diffusionEndDate = new DateTime();
        $diffusionEndDate->modify('+30 year');
        $this->fileDiffusionEnd = $diffusionEndDate;

        $filename = $taskInfo['fileName'];

        // get dynamically the right EntityManager based on customer name
        $this->entityManager = $managerRegistry->getManager(strtolower($this->customer));

        //$this->repository = new media_rep($this->getCustomerBase($customer));
        $this->repository = $this->entityManager->getRepository(Media::class);
        //$this->repository->setBase($this->getCustomerBase($this->customer));

        $options = ['diff' => 'medias', 'them' => 'thematics', 'sync' => 'synchros'];
        $authorized_files = ['mp4', 'x-matroska', 'avi', 'x-quicktime', 'quicktime', 'bmp', 'png', 'jpg', 'jpeg'];
        $type_dir = $options[$this->mediatype];

        $this->srcfolder = $this->parameterBag->get('project_dir') . '/../node_file_system';

        if($this->mediatype != 'sync') {
            $this->srcfolder .= '/' . $this->customer . '/' . $type_dir . '/';
            $this->destfolder = $this->customer_dir . 'medias/';
        } else {
            //$this->srcfolder = 'C:/inetpub/wwwroot/admin/node_JS/node_ftp_server/temp/';
            $this->srcfolder .= '/../inetpub/wwwroot/admin/node_JS/node_ftp_server/temp/';
            //$this->destfolder = 'C:/inetpub/wwwroot/upload/' . $customer . '/';
            $this->destfolder = $this->parameterBag->get('project_dir') . '/../inetpub/wwwroot/upload/' . $this->customer . '/';
            $authorized_files = ['mp4', 'x-matroska', 'avi', 'x-quicktime', 'quicktime'];
        }
        if($this->mediatype == 'them') {
            //$this->destfolder = 'C:/inetpub/wwwroot/upload/thematics/';
            $this->destfolder = $this->parameterBag->get('project_dir') . '/../inetpub/wwwroot/upload/thematics/';
            $authorized_files = ['mp4', 'x-matroska', 'avi', 'x-quicktime', 'quicktime'];
        }

        $path = $this->srcfolder . $filename;

        $last_dot_pos = strrpos($filename, '.');
        //$this->extension = substr($filename, $last_dot_pos+1);
        $this->extension = $taskInfo['extension'];
        $this->filename = substr($filename, 0, $last_dot_pos);
        //$this->originalFileName = $filename;

        if (file_exists($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $path);
            $splash = explode('/', $mimeType);
            $this->filetype = $splash[0];
            $real_file_extension = $splash[1];

            if(!in_array($real_file_extension, $authorized_files)) {
                $this->errors[] = 'permission denied - ' . $real_file_extension . ' bad extension';
                return;
            } else {
                $this->process($path);
            }
        } else {
            dd($path);
            $this->errors[] = 'file not found!';
            return;
        }

    }


    private function process($media){

        $valid_ext = ''; $complete = false;

        switch($this->mediatype) {
            case 'diff':
                if ($this->filetype == 'image') {
                    $complete = $this->imageResize($media);
                    $complete = true;
                    $valid_ext = 'png';
                    $old_path = $this->customer_dir . 'IMAGES/PRODUITS FIXES/PLEIN ECRAN/';
                }
                if ($this->filetype == 'video') {
                    $complete = $this->videoEncoding($media);
                    $valid_ext = 'mp4';
                    $old_path =  $this->customer_dir . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/HIGH/';
                }

                if($complete) { // Attention, le retour des fonctions videoEncoding() et imageResize() n'est pas toujours une variable booléenne !!

                    // On récupère les informations du nouveau média et on l'insère en base
                    $this->retrieveInfo($media);

                    // On remplace le nom original des différentes résolutions créées par l'id du media
                    $sizes = ['low', 'medium', 'high', 'HD'];
                    foreach ($sizes as $size) {
                        $dir_ref = $this->destfolder . "/$size/" . $this->filename . '.' . $valid_ext;
                        if(file_exists($dir_ref)) {
                            rename($dir_ref, $this->destfolder . "/$size/" . $this->fileID . '.' . $valid_ext);
                            if($size == 'high') {
                                $this->repository->updateHigh($this->fileID);
                            }
                        }
                    }
                    unlink($media);
                    return true;
                } else {
                    unlink($media);
                    // $this->errors[] = 'permission denied - bad resolution';
                    return $complete;
                }
                break;
            case 'sync':
                // la copie des fichiers dans les anciens répertoires peut ici se faire après chaque traitement de vidéo mais il sera nécessaire de faire une boucle unique sur l'ensemble des vidéos de la synchro dès le début du processus (ftp-server)
                // pour récupérer l'événement de finalisation de l'encodage et ainsi insérer la synchro en base (en tant qu'entité synchro)
                if ($this->filetype != 'video') {
                    unlink($media);
                    return false;
                }
                $complete = $this->videoEncoding($media);

                if($complete) {
                    $this->retrieveInfo($media);
                    $sizes = ['low', 'medium', 'high', 'HD'];
                    foreach ($sizes as $size) {
                        $temp_dir = $this->destfolder . "$size/" . $this->filename . '.mp4';
                        $final_dir = $this->customer_dir . 'medias/video';
                        if(file_exists($temp_dir)) {
                            rename($temp_dir, $final_dir . "/$size/" . $this->fileID . '.mp4');
                            if($size == 'high') {
                                $this->repository->updateHigh($this->fileID);
                            }
                        }
                    }
                    // Here the code for duplicate the folder in videos/sync !
                    unlink($media);
                    return true;
                } else {
                    unlink($media);
                    return false;
                }
                break;
            case 'them':
                if ($this->filetype != 'video') {
                    unlink($media);
                    return false;
                }
                $complete = $this->videoEncoding($media);
                if($complete) {
                    $this->retrieveInfo($media);
                    $sizes = ['low', 'medium', 'high'];
                    foreach ($sizes as $size) {
                        $dir_ref = $this->destfolder . "$size/" . $this->filename . '.mp4';
                        if(file_exists($dir_ref)) {
                            rename($dir_ref, $this->destfolder . "$size/" . $this->fileID . '.mp4');
                        }
                    }
                    // La source du média est conservée dans wwwroot/upload/thematics
                    rename($this->srcfolder . $this->filename . '.' . $this->extension, $this->destfolder . 'source/' . $this->fileID . '.mp4');
                    return true;
                } else {
                    unlink($media);
                    return false;
                }
                break;
            case 'elmt':
                $format_not_standard = false;
                $this->retrieveInfo($media);
                if($this->filetype == 'image' && $this->extension != 'png') {
                    $source = imagecreatefromstring(file_get_contents($media));
                    imagepng($source, $this->destfolder . 'piece/' . $this->fileID . '.png');
                    $format_not_standard = true;
                }
                if($this->filetype == 'video' && $this->extension != 'mp4') {
                    exec('ffmpeg -y -i "' . $media . '" -vcodec libx264 -acodec copy -y "' . $this->destfolder . 'piece/' . $this->fileID . '.mp4"');
                    $format_not_standard = true;
                }
                if($format_not_standard) {
                    unlink($media);
                } else {
                    rename($media, $this->destfolder . 'piece/' . $this->fileID . '.' . $this->extension);
                }
                return true;
                break;
            default:
                return false;
        }
    }

    public function OldvideoEncoding($video)
    {
        if($this->mediatype != 'them') {
            $this->destfolder .= 'video/';
        }

        $resolution = $this->getVideoDimensions($video);
        $width = $resolution['width'];
        $height = $resolution['height'];
        $ratio = $width / $height;
        if($ratio > 1) {
            $this->syncOri = 'horizontal';
        } else {
            $this->syncOri = 'vertical';
        }
        $HD = false;
        $max_size = false;
        $medium_size = false;
        $output_high = '';
        $output_medium = '';
        $output_low = '';

        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal
                if ($width >= 1920 && $height >= 1080) {
                    $max_size = true;   // Il faut réencoder la vidéo en résolution high quoi qu'il arrive
                    $output_high = "1920*1080";
                    if ($width > 1920 && $height > 1080) {
                        $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                    }
                }
                if($width >= 1280 && $height >= 720) {
                    $output_medium = "1280*720";
                    $medium_size = true;
                }
                $output_low = "160*90";
                break;

            case 9 / 16:   // Plein Ecran Vertical
                if ($width >= 1080 && $height >= 1920) {
                    $max_size = true;
                    $output_high = "1080*1920";
                    if ($width > 1080 && $height > 1920) {
                        $HD = true;
                    }
                }
                if($width >= 720 && $height >= 1280) {
                    $output_medium = "720*1280";
                    $medium_size = true;
                }
                $output_low = "90*160";
                break;

            case 9 / 8:  // Demi Ecran Horizontal
                if ($width >= 1080 && $height >= 960) {
                    $max_size = true;
                    $output_high = "1080*960";
                    if ($width > 1080 && $height > 960) {
                        $HD = true;
                    }
                }
                if($width >= 720 && $height >= 640) {
                    $medium_size = true;
                    $output_medium = "720*640";
                }
                $output_low = "90*80";
                break;

            case 8 / 9:  // Demi Ecran Vertical
                if ($width >= 960 && $height >= 1080) {
                    $max_size = true;
                    $output_high = "960*1080";
                    if ($width > 960 && $height > 1080) {
                        $HD = true;
                    }
                }
                if ($width >= 640 && $height >= 720) {
                    $medium_size = true;
                    $output_medium = "640*720";
                }
                $output_low = "80*90";
                break;

            default:
                // case élément graphique
                if($this->mediatype == 'elmt') {
                    $low_width = round($width/($height/base_height));
                    $medium_width =  round($width/($height/500));
                    if($medium_width%2 == 1) {
                        $medium_width++;
                    }
                    $output_medium = $medium_width . '*500';
                    $medium_size = true;
                    $output_low = $low_width . '*' . base_height;
                    // change dir !?
                }
                // case média diffusable
                if($this->mediatype == 'diff') {
                    return false; // $ratio?
                    // A quel moment check t-on si la résolution du média est suffisante pour créer une vignette ??
                }
                break;
        }

        $response = [];
        $error = [];
        if ($max_size) {
            // -preset medium, -compression_level, -crf 20 = Constante Rate Factor ??
            // -b:v 20M (pour les dias converties en vidéo) -g 2
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_high . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -acodec copy -y "' . $this->destfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
            if($this->mediatype != 'them') {
                // Copie du fichier encodé vers l'ancien répertoire correspondant pour les médias diffusables
                if($this->mediatype != 'sync') {
                    $old_path = $this->customer_dir . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/HIGH/';
                    copy($this->destfolder . 'high/' . $this->filename . '.mp4', $old_path . $this->filename . '.mp4');
                }
                if($HD) {
                    // Copie (déplacement impossible à ce stade) de la source vers le dossier HD
                    copy($video, $this->destfolder . 'HD/' . $this->filename . '.mp4');
                }
            }
        } else {return false;}

        if($medium_size) {
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_medium . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -acodec copy -y "' . $this->destfolder . 'medium/' . $this->filename . '.mp4"', $response['medium'], $error['medium']);
            if($this->mediatype != 'them') {
                // Copie du fichier encodé vers l'ancien répertoire correspondant pour les médias diffusables
                if($this->mediatype != 'sync') {
                    $old_path = $this->customer_dir . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/';
                    copy($this->destfolder . 'medium/' . $this->filename . '.mp4', $old_path . $this->filename . '.mp4');
                }
            }

            // Création de la vignette si au moins le format "medium" existe
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_low . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -acodec copy -y "' . $this->destfolder . 'low/' . $this->filename . '.mp4"', $response['low'], $error['low']);
        }

        foreach($error as $bug) {
            /*
            Un statut égal à 0 signifie un succès de l’opération (échec s’il est différent)
            Return values are completely arbitrary. When you write a program you can make it return whatever value you want. ex: exit(33);
            You can find out the exact status code for a specific piece of software on its documentation !
            */
            if($bug != 0) {  // $bug value seems to be always 0 or false so never return message !
                // return 'error!';
            }
        }
        return true;
    }

    public function videoEncoding($video) {

        if($this->mediatype != 'them') {
            $this->destfolder .= 'video/';
        }

        $resolution = $this->getVideoDimensions($video);
        $width = $resolution['width'];
        $height = $resolution['height'];
        $ratio = $width / $height;
        if($ratio > 1) {
            $this->syncOri = 'horizontal';
        } else {
            $this->syncOri = 'vertical';
        }
        $HD = false;
        $max_size = false;
        $medium_size = false;
        $output_high = '';
        $output_medium = '';
        $output_low = '';

        // Old path
        if($this->customer == '5asec') {
            $old_path = $this->customer_dir . 'AUTRES/';
            if($width > $height){
                $old_path .= 'VIDEOS HORIZONTALES/';
            } else {
                $old_path .= 'VIDEOS VERTICALES/';
            }
        } else {
            $old_path = $this->customer_dir . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/';
        }

        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal

                if($width < 1920 && $height < 1080) {
                    $this->errors[] = 'permission denied - bad resolution - format minimum: 1920 x 1080';
                    return false;
                }

                if ($width > 1920 && $height > 1080) {
                    $max_size = true;
                    $output_high = "1920*1080";
                    $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                }

                if ($width == 1920 && $height == 1080) {
                    $max_size = false;
                    // On copie simplement la vidéo sans la réencoder dans les répertoires Tizen et LFD!
                    //dd($video, $this->destfolder . 'high/' . $this->filename . '.mp4');
                    copy($video, $this->destfolder . 'high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                        copy($video, $old_path . 'HIGH/' . $this->filename . '.mp4');
                    }
                }

                if($width >= 1280 && $height >= 720) {
                    $output_medium = "1280*720";
                    $medium_size = true;
                }
                $output_low = "160*90";
                break;

            case 9 / 16:   // Plein Ecran Vertical

                if($width < 1080 && $height < 1920) {
                    $this->errors[] = 'permission denied - bad resolution - format minimum: 1080 x 1920';
                    return false;
                }

                if ($width > 1080 && $height > 1920) {
                    $max_size = true;
                    $output_high = "1080*1920";
                    $HD = true;
                }

                if ($width == 1080 && $height == 1920) {
                    $max_size = false;
                    copy($video, $this->destfolder . 'high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                        copy($video, $old_path . 'HIGH/' . $this->filename . '.mp4');
                    }
                }

                if($width >= 720 && $height >= 1280) {
                    $output_medium = "720*1280";
                    $medium_size = true;
                }
                $output_low = "90*160";
                break;

            case 9 / 8:  // Demi Ecran Horizontal

                if($width < 1080 && $height < 960) {
                    $this->errors[] = 'permission denied - bad resolution - format minimum: 1080 x 960';
                    return false;
                }

                if ($width > 1080 && $height > 960) {
                    $max_size = true;
                    $output_high = "1080*960";
                    $HD = true;
                }

                if ($width == 1080 && $height == 960) {
                    $max_size = false;
                    copy($video, $this->destfolder . 'high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                        copy($video, $old_path . 'HIGH/' . $this->filename . '.mp4');
                    }
                }

                if($width >= 720 && $height >= 640) {
                    $medium_size = true;
                    $output_medium = "720*640";
                }
                $output_low = "90*80";
                break;

            case 8 / 9:  // Demi Ecran Vertical

                if ($width < 960 && $height < 1080) {
                    $this->errors[] = 'permission denied - bad resolution - format minimum: 960 x 1080';
                    return false;
                }

                if ($width > 960 && $height > 1080) {
                    $max_size = true;
                    $output_high = "960*1080";
                    $HD = true;
                }

                if ($width == 960 && $height == 1080) {
                    $max_size = false;
                    copy($video, $this->destfolder . 'high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                        copy($video, $old_path . 'HIGH/' . $this->filename . '.mp4');
                    }
                }

                if ($width >= 640 && $height >= 720) {
                    $medium_size = true;
                    $output_medium = "640*720";
                }
                $output_low = "80*90";
                break;

            default:
                if($this->mediatype == 'diff') {
                    return false;
                }
                break;
        }

        if ($max_size) {
            // -preset medium, -compression_level, -crf 20 = Constante Rate Factor ??
            // -b:v 20M (pour les dias converties en vidéo) -g 2
            // -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_high . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $this->destfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
            if($this->mediatype != 'them') {
                // Copie du fichier encodé vers l'ancien répertoire correspondant pour les médias diffusables
                if($this->mediatype != 'sync') {
                    copy($this->destfolder . 'high/' . $this->filename . '.mp4', $old_path . 'HIGH/' . $this->filename . '.mp4');
                    // file_put_contents(__DIR__ . '/../log/upload.log', 'FTP://La video ' . $this->filename . '.mp4 de format high et se trouvant dans ' . $this->destfolder . 'high/ a ete recopiee dans le repertoire ' . $old_path . ' avec le nom ' . $this->filename . '.mp4' . PHP_EOL, FILE_APPEND);
                }
                if($HD) {

                    // if end with video/
                    if (substr($this->destfolder, -strlen('video/')) === 'video/')
                        copy($video, $this->destfolder . 'HD/' . $this->filename . '.mp4');

                    else
                        // Copie (déplacement impossible à ce stade) de la source vers le dossier HD
                        copy($video, $this->destfolder . 'video/HD/' . $this->filename . '.mp4');
                }
            }
        }

        if($medium_size) {
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_medium . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $this->destfolder . 'medium/' . $this->filename . '.mp4"', $response['medium'], $error['medium']);
            if($this->mediatype != 'them') {
                // Copie du fichier vers l'ancien répertoire correspondant pour les médias diffusables
                if($this->mediatype != 'sync') {
                    copy($this->destfolder . 'medium/' . $this->filename . '.mp4', $old_path . $this->filename . '.mp4');
                    // file_put_contents(__DIR__ . '/../log/upload.log', 'FTP://La video ' . $this->filename . '.mp4 de format medium et se trouvant dans ' . $this->destfolder . 'medium/ a ete recopiee dans le repertoire ' . $old_path . ' avec le nom ' . $this->filename . '.mp4' . PHP_EOL, FILE_APPEND);
                }
            }

            // Création de la vignette si au moins le format "medium" existe
            exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_low . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $this->destfolder . 'low/' . $this->filename . '.mp4"', $response['low'], $error['low']);
        }
        return true;
    }

    public function getSyncOrientation() {
        return $this->syncOri;
    }

    private function imageResize($img)
    {
        $this->destfolder .= 'image/';
        list($width, $height) = getimagesize($img);
        $output = array();
        $ratio = $width / $height;
        $source = null;

        if($this->extension == 'png') {
            $source = imagecreatefrompng($img);
        } else {
            $source = imagecreatefromstring(file_get_contents($img));
        }

        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal

                if ($width >= 1920 && $height >= 1080) {
                    $output['high'][0] = 1920;
                    $output['high'][1] = 1080;
                    if ($width > 1920 && $height >1080) {
                        imagepng($source, $this->destfolder . 'HD/' . $this->filename . '.png');
                    }
                }

                if ($width >= 1280 && $height >= 720) {
                    $output['medium'][0] = 1280;
                    $output['medium'][1] = 720;
                }

                if ($width >= 160 && $height >= 90) {
                    $output['low'][0] = 160;
                    $output['low'][1] = 90;
                }

                break;

            case 9 / 16:   // Plein Ecran Vertical

                if ($width >= 1080 && $height >= 1920) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 1920;
                    if ($width > 1080 && $height >1920) {
                        imagepng($source, $this->destfolder . 'HD/' . $this->filename . '.png');
                    }
                }

                if ($width >= 720 && $height >= 1280) {
                    $output['medium'][0] = 720;
                    $output['medium'][1] = 1280;
                }

                if ($width >= 90 && $height >= 160) {
                    $output['low'][0] = 90;
                    $output['low'][1] = 160;
                }

                break;

            case 9 / 8:  // Demi Ecran Horizontal

                if ($width >= 1080 && $height >= 960) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 960;
                    if ($width > 1080 && $height > 960) {
                        imagepng($source, $this->destfolder . 'HD/' . $this->filename . '.png');
                    }
                }

                if ($width >= 720 && $height >= 640) {
                    $output['medium'][0] = 720;
                    $output['medium'][1] = 640;
                }

                if ($width >= 90 && $height >= 80) {
                    $output['low'][0] = 90;
                    $output['low'][1] = 80;
                }

                break;

            case 8 / 9:  // Demi Ecran Vertical

                if ($width >= 960 && $height >= 1080) {
                    $output['high'][0] = 960;
                    $output['high'][1] = 1080;
                    if ($width > 960 && $height > 1080) {
                        imagepng($source, $this->destfolder . 'HD/' . $this->filename . '.png');
                    }
                }

                if ($width >= 640 && $height >= 720) {
                    $output['medium'][0] =640;
                    $output['medium'][1] = 720;
                }

                if ($width >= 80 && $height >= 90) {
                    $output['low'][0] = 80;
                    $output['low'][1] = 90;
                }

                break;

            default:  // situation où le ratio n'est pas standardisé
                // case élément graphique
                if($this->mediatype == 'elgp') {
                    // Why encoding more graphic element ??
                    $low_width = round($width/($height/base_height));
                    $output_low = $low_width . '*' . base_height;
                    $output['low'][0] = $output_low;
                    $output['low'][1] = base_height;
                }
                // case média diffusable
                if($this->mediatype == 'diff') {
                    // unlink($img);   [fonction déjà impléméntée si return false]
                    return false; // On exclut l'insertion en base
                }
                break;
        }

        $format = array('high', 'medium', 'low');

        for ($i = 0; $i < 3; $i++) {
            if (isset($output[$format[$i]])) {
                // On vérifie que l'image a une résolution suffisante pour être redimensionnée
                $thumb = imagecreatetruecolor($output[$format[$i]][0], $output[$format[$i]][1]);
                //imagealphablending($thumb, false);
                //imagesavealpha($thumb, true);
                // $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                $transparent = imagecolorallocate($thumb, 255, 255, 255);
                imagefilledrectangle($thumb, 0, 0, $output[$format[$i]][0], $output[$format[$i]][1], $transparent);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $output[$format[$i]][0], $output[$format[$i]][1], $width, $height);
                imagepng($thumb, $this->destfolder .  $format[$i] . '/' . $this->filename . '.png', 9, PNG_ALL_FILTERS);
                if($i == 0) {
                    // Théoriquement, basculer le média avec son nom original dans l'ancien répertoire est suffisant pour qu'il puisse être exploitable dans l'ancienne application. Mais il faut s'assurer, en revanche, que les incrustes soient transposées sous les 2 nominations possibles (id & nom) puisque le répertoire des incrustes est identique pour les 2 applications. [voir si l'on peut créer un repertoire spécifique dans médias pour la newApp]
                    $old_path = $this->customer_dir . 'IMAGES/PRODUITS FIXES/PLEIN ECRAN/';
                    copy($this->destfolder .  $format[$i] . '/' . $this->filename . '.png', $old_path . $this->filename . '.png');
                }
            }
        }
        return true;
    }

    public function getVideoDimensions($file) {
        //$path_to_json = 'C:\inetpub\wwwroot\tmp\infofile.json';
        $path_to_json = $this->parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
        exec('ffprobe -v quiet -print_format json -show_format -show_streams "'. $file . '" > "' . $path_to_json . '"');
        $data = file_get_contents($path_to_json);
        $json = (array) json_decode($data);
        $result = array('width' => $json['streams'][0]->width, 'height' => $json['streams'][0]->height);
        return $result;
    }

    public function retrieveInfo($src)
    {
        $infofile = array();
        $newMedia = new Media();
        $newThematic = null;
        //$newTemplateContent = new TemplateContents();

        try
        {

            if ($this->filetype == 'video') {
                $newVideo = new Video();
                /* Création librairie php-ffmpeg en passant par la ligne de commande pour éviter les problèmes d'incompatibilité */
                //$path_to_json = 'C:\inetpub\wwwroot\tmp\infofile.json';
                $path_to_json = $this->parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
                exec('ffprobe -v quiet -print_format json -show_format -show_streams "' . $src . '" > "' . $path_to_json . '"', $output, $error);

                if (!$error) {
                    $datafile = file_get_contents($path_to_json);
                    $flux = (array)json_decode($datafile);
                } else {
                    return false;
                }
                $video = $audio = $format = [];
                if (isset($flux['streams'][0])) {
                    $video = $flux['streams'][0];
                }
                if (isset($flux['streams'][1])) {
                    $audio = $flux['streams'][1];
                }
                if (isset($flux['format'])) {
                    $format = $flux['format'];
                }
                $filter = array('codec_long_name', 'width', 'height', 'bit_rate', 'bits_per_raw_sample', 'nb_frames', 'creation_time', 'encoder', 'duration', 'level', 'avg_frame_rate', 'filename', 'format_long_name', 'size', 'major_brand', 'sample_rate', 'channels');

                foreach ($video as $key => $value) {
                    if (in_array($key, $filter)) {
                        $infofile['video'][$key] = $value;
                    }
                    if ($key == 'tags') {
                        foreach ($value as $label => $info) {
                            if (in_array($label, $filter)) {
                                $infofile['video'][$label] = $info;
                            }
                        }
                    }
                }
                foreach ($format as $key => $value) {
                    if (in_array($key, $filter)) {
                        $infofile['video'][$key] = $value;
                    }
                    if ($key == 'tags') {
                        foreach ($value as $label => $info) {
                            if (in_array($label, $filter)) {
                                $infofile['video'][$label] = $info;
                            }
                        }
                    }
                }

                foreach ($audio as $key => $value) {
                    if (in_array($key, $filter)) {
                        $infofile['audio'][$key] = $value;
                    }
                }
                unset($infofile['audio']['avg_frame_rate'], $infofile['audio']['duration']);
                $ratio = $this->EstablishFormat($infofile['video']['width'], $infofile['video']['height']);

                /* Instanciation de la classe video */
                if ($video != null) {
                    $data = $infofile['video'];
                    $filepath = explode('/', $data['filename']);
                    $splash = explode('.', end($filepath)); // Attention aux fichiers avec plusieurs points dans le nom => passer par strrpos !
                    $file = $splash[0];
                    $ext = $splash[1];
                    $level_array = str_split($data['level']);
                    $level = implode('.', $level_array);

                    if ($this->mediatype != 'them') {

                        /*$newTemplateContent->setName($file);
                        $newTemplateContent->setContentType('video');
                        $this->fileID = $this->repository->insert($newTemplateContent);
                        $templateContentId = $this->fileID;*/

                        //$newMedia->setType($this->mediatype);
                        $newVideo->setSize(round($data['size'] / (1024 * 1024), 2) . ' Mo')
                            ->setType($this->mediatype)
                            ->setCreatedAt(new DateTime())
                            ->setDiffusionStart($this->fileDiffusionStart)
                            ->setDiffusionEnd($this->fileDiffusionEnd)
                            ->setHeight($data['height'])
                            ->setWidth($data['width'])
                            ->setName($file)
                            ->setExtension($ext)
                            ->setFormat($data['major_brand'])
                            ->setRatio($ratio)
                            ->setSampleSize($data['bits_per_raw_sample'] . ' bits')
                            ->setEncoder($data['encoder'])
                            ->setVideoCodec($data['codec_long_name'])
                            ->setVideoCodecLevel($level)
                            ->setVideoFrequence(substr($data['avg_frame_rate'], 0, -2) . ' img/s')
                            ->setVideoFrame($data['nb_frames'])
                            ->setVideoDebit((int)($data['bit_rate'] / 1000) . ' kbit/s')
                            ->setDuration(round($data['duration'], 2) . ' secondes');

                    } else {
                        $newThematic = [
                            'filename' => $file,
                            'extension' => $ext,
                            'size' => round($data['size'] / (1024 * 1024), 2),
                            'format' => $data['major_brand'],
                            'ratio' => $ratio,
                            'height' => $data['height'],
                            'width' => $data['width'],
                            'sampleSize' => $data['bits_per_raw_sample'] . ' bits',
                            'encoder' => $data['encoder'],
                            'videoCodec' => $data['codec_long_name'],
                            'videoCodecLevel' => $level,
                            'videoFrequence' => substr($data['avg_frame_rate'], 0, -2) . ' img/s',
                            'videoFrames' => $data['nb_frames'],
                            'videoDebit'=> (int)($data['bit_rate'] / 1000) . ' kbit/s',
                            'duration' => round($data['duration'], 2),
                            'theme' => null,
                            'date' => date('Y-m-d')
                        ];
                    }

                    if ($audio != null) {
                        $data = $infofile['audio'];
                        if ($this->mediatype != 'them') {
                            $newVideo->setAudioCodec($data['codec_long_name']);
                            $newVideo->setAudioDebit((int)($data['bit_rate'] / 1000) . ' kbit/s');
                            $newVideo->setAudioFrequence($data['sample_rate'] . ' Hz');
                            $newVideo->setAudioChannel($data['channels']);
                            $newVideo->setAudioFrame($data['nb_frames']);
                        } else {
                            $newThematic['audioCodec'] = $data['codec_long_name'];
                            $newThematic['audioDebit'] = (int)($data['bit_rate'] / 1000) . ' kbit/s';
                            $newThematic['audioFrequence'] = $data['sample_rate'] . ' Hz';
                            $newThematic['audioChannel'] = $data['channels'];
                            $newThematic['audioFrames'] = $data['nb_frames'];
                        }
                    }
                    if ($this->mediatype != 'them') {
                        $this->error = $this->repository->insert($newVideo);
                    } else {
                        $th_rep = new theme_rep();
                        $this->fileID = $th_rep->saveVideoThematic($newThematic);
                    }
                }
            }

            if($this->filetype == 'image') {

                $newImg = new Image();

                list($width, $height) = getimagesize($src);
                $ratio = $this->EstablishFormat($width, $height);
                $newImg->setName($this->filename)
                       ->setType($this->mediatype)
                       ->setRatio($ratio)
                       ->setExtension($this->extension)
                       ->setWidth($width)
                       ->setHeight($height)
                       ->setSize(round(filesize($src)/(1024*1024), 2) . ' Mo')
                       ->setCreatedAt(new DateTime())
                       ->setDiffusionStart($this->fileDiffusionStart)
                       ->setDiffusionEnd($this->fileDiffusionEnd);

                try
                {
                    $this->repository->insert($newImg);
                }
                catch (Exception $e)
                {
                    throw new Exception($e->getMessage());
                }

            }

        }
        catch (Exception $e)
        {
            throw new Exception(sprintf("Internal Error : impossible to insert media ! Cause : '%s'", $e->getMessage()));
        }

    }

    public function EstablishFormat($width, $height) {
        $ratio = $width / $height;

        switch ($ratio) {
            case 16 / 9:
                $format = '16/9';
                break;
            case 9 / 16:
                $format = '9/16';
                break;
            case 9 / 8;
                $format = '9/8';     // modif
                break;
            case 8 / 9:
                $format = '8/9';       // modif
                break;
            default:
                $format = 'non supporté!';
        }
        return $format;
    }

    public function ReloadMediaList($filter = null) // ['orientation' => 'horizontal', 'format' => 'plein-écran', 'type' => 'image']
    {
        //include __DIR__ . '/../config/include_piw.php';
        $filter_result = $this->get_url_data('getFilterMedia', $filter, $_SESSION['QUICKNET']['base']);
        //$assocMedias = $this->repository->getAssociatedMedia();
        return $filter_result;
    }

    public function extract_frame($video_id) {
        /*
        $path = 'c:/xampp/upload/frame/';
        $ffprobe = FFMpeg\FFProbe::create();
        $format = $ffprobe
            ->format($file);
        $duration =  round($format->get('duration'), 2);
        $timeframe = $duration - 0.5;
        $ffmpeg = FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($file);
        $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($timeframe));
        $frame->save($path . $this->fileID . '.png');
        */
        $rep = new ClientSideRepository();
        $filename = $this->repository->getFileName($video_id) . '.png';
        $duration = $rep->getVideoDuration($video_id);
        $file = $this->destfolder . 'video/high/' . $video_id . '.mp4';
        $time = $duration - 1;
        $path_to_ffmpeg = __DIR__ . '/../../../bin/';
        $path_to_warehouse = $this->destfolder . 'frame/' . $filename;
        $cmd = $path_to_ffmpeg . 'ffmpeg -ss ' . $time . ' -i "' . $file . '" -t 1 -f image2 "' . $path_to_warehouse . '"';
        exec($cmd, $output, $error);
    }

    public function get_url_data($AjaxFileName, array $params = [], $client = '') {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/ajax/' . $AjaxFileName . '.php' ;
        if(count($params) > 0) {
            $url .= '?';
            foreach ($params as $parameter => $value) {
                $url .= $parameter . '=' . $value . '&';
            }
            if($client != '') {
                $url .= 'client=' . $client;
            } else {
                $url = substr($url, 0, -1);
            }
        }
        //var_dump($url);
        $ch = curl_init();
        $timeout = 100;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        if (!$data) {
            $data = curl_error($ch);
        }
        curl_close($ch);
        return $data;
    }


    private function getCustomerDirectory($customer) {
        //$conf = Yaml::parse(file_get_contents(__DIR__ . '/../config/parameters.yml'));
        $conf = $this->parameterBag->get('sys_path');
        $data_dir = 'data_' . $customer;
        // Exceptions
        switch($customer) {
            case 'quick':
                $data_dir = 'data';
                break;
            case 'domtom':
                $data_dir = 'data_dt';
            case 'generique':
                $data_dir = 'data_gen';
                break;
        }
        //return $conf['sys_path']['datas'] . '/' . $data_dir . '/PLAYER INFOWAY WEB/';
        return $conf['datas'] . '/' . $data_dir . '/PLAYER INFOWAY WEB/';
    }

    private function getCustomerBase($customer) {
        $base = 'quicknet';
        if($customer != 'quick') {
            $base = $customer;
        }
        return $base;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}