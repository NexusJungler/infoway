<?php
ini_set('memory_limit', '-1');
if (!isset($_SESSION)) {
    session_start();
}

require_once(__DIR__ . '/../repository/media_rep.php');
require_once(__DIR__ . '/ClientSideRepository.php');
require_once(__DIR__ . '/media.php');
require_once(__DIR__ . '/image.php');
require_once(__DIR__ . '/video.php');
require_once(__DIR__ . '/TemplateContents.php');

$_SESSION['client_dir'] = substr(dirname($_SESSION['QUICKNET']['RES_rep']), 3);   // Parameter "levels" added in php 7.0  !


class UploadEncode
{
    public $fileID;
    private $error;
    private $filetype;
    private $mediatype;
    private $filename;
    private $extension;
    private $destfolder;
    private $srcfolder =  'C:/inetpub/wwwroot/upload/';
    private $repository;
    private $filterDatas = [];
    private $updated = false;
    private $existingMedias = [];
    private $newFileName = null;

    public function __construct($media_type,$file=false)
    {
        $search = explode('_', $_SESSION['client_dir']);
        if(count($search) == 2) {
            $client = $search[1];
        } else {
            if(count($search) == 3) {
                $client = $search[1] . '_' . $search[2];
            } else {
                $client = 'quick';
            }
        }
        if($file) {
            $this->fileID = $file['id'];
            $this->updated=$file['name'];
        }
        $this->mediatype = $media_type;
        $this->repository = new media_rep();
        foreach($this->repository->selectAllMediaName() as $mediaName)
        {

            if($mediaName['type'] !=='sync') {
                $this->existingMedias[] = $mediaName['filename'];
            }
        }

        $this->destfolder = $_SESSION['QUICKNET']['RES_rep'] . 'medias/';
        if($media_type == 'them') {
            $this->srcfolder .= 'thematics/';
        } else {
            $this->srcfolder .= $client . '/';
        }
    }


    public function setFilterDatas($orientation,$format,$type){
        $this->filterDatas['orientation']=$orientation;
        $this->filterDatas['format']=$format;
        $this->filterDatas['type']=$type;
    }

    public function process($mediaInfos){
        $media = $mediaInfos['path'];
        $dir_src = explode('/', $media);
        $file = end($dir_src);
        // l'extension des fichiers uploadés est déjà contrôlée en Javascript (fichiers sans extension exclus)
        $last_dot_pos = strrpos($file, '.');
        $this->extension = substr($file, $last_dot_pos+1);
        $this->filename = substr($file, 4, - (strlen($this->extension)+1)); // On retire le 'tmp_' généré par le process d'upload ainsi que l'extension de fichier
        // $this->filetype = 'image';  // DEBUG

        if(!$this->updated && $this->mediatype !== 'sync' && in_array($this->filename,$this->existingMedias)){
//            $this->error['error'][$this->filename][]='Erreur : Un media portant le même nom existe deja';
            $this->error=null;
            $this->error[$this->filename.'.'.$this->extension][]='Un media portant le même nom existe deja !';
            return ['error'=>$this->error];
        }
        $this->filetype = $mediaInfos['type'];  // WARNING! $complete vaudra null si $_SESSION['type_file'] n'existe pas !!
        $valid_ext = ''; $complete = false;

        if($this->updated){
            $this->newFileName = $this->updated;
        } else {
            $this->newFileName = $this->filename;
        }

        switch($this->mediatype) {
            case 'diff':
                if ($this->filetype == 'image') {
                    $complete = $this->imageResize($media);
                    $valid_ext = 'png';
                    $old_path = $_SESSION['QUICKNET']['RES_rep'] . 'IMAGES/PRODUITS FIXES/PLEIN ECRAN/';
                }
                if ($this->filetype == 'video') {
                    $complete = $this->videoEncoding($media);
                    $valid_ext = 'mp4';
                    $old_path = $_SESSION['QUICKNET']['RES_rep'] . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/HIGH/';
                }

                if($complete) { // Attention, le retour des fonctions videoEncoding() et imageResize() n'est pas toujours une variable booléenne !!
                    // On récupère les informations du nouveau média et on l'insère en base
                    $this->retrieveInfo($media);

                    // On remplace le nom original de la source par l'id du media
                    // rename($media, $this->srcfolder . $this->filetype . '/source/' . $this->fileID . '.' . $this->extension);
                    unlink($media);

                    // On remplace le nom original des différentes résolutions créées par l'id du media
                    $sizes = ['low', 'medium', 'high', 'HD'];
                    foreach ($sizes as $size) {
                        $dir_ref = $this->destfolder . $this->filetype . "/$size/" . $this->filename . '.' . $valid_ext;
                        if(file_exists($dir_ref)) {
                            rename($dir_ref, $this->destfolder . $this->filetype . "/$size/" . $this->fileID . '.' . $valid_ext);
                            if($size == 'high') {
                                $this->repository->updateHigh($this->fileID);
                            }
                        }
                    }

                    // Attention, à ce stade, on ne sait pas quelle résolution a été uploadée originalement, peut-être 4K !! De plus, les vidéos doivent être dupliquées pour le format "high" mais aussi "medium" !! La duplication dans les répertoires valides UC a donc été déplacée au moment où l'encodage de chaque résolution se termine.
                    return true;
                } else {
                    unlink($media); // Attention, seule la source sera effacée du server mais certains formats dérivés ont pu être encodés et resteront sur le server!!
                    return $this->error;
                }
                break;
            case 'sync':
                // Attention, la copie des fichiers dans les anciens répertoires ne peut se faire qu'au moment de la validation de la synchro, c'est-à-dire après l'upload ! Propriétés sync_name et sync_index inutiles, il faut créer un nouveau fichier Ajax à part => validateSynchro.php
                if ($this->filetype != 'video') {
                    unlink($media);
                    $this->error[$this->filename.'.'.$this->extension][]= 'Erreur : Le media que vous tentez de telecharger n\'est pas une vidéo !';
                    return ['error'=>$this->error];
                }
                $complete = $this->videoEncoding($media);
                if($complete) {
                    $this->retrieveInfo($media);
                    unlink($media);
                    // rename($media, $this->srcfolder . $this->filetype . '/source/' . $this->fileID . '.' . $this->extension);
                    $sizes = ['low', 'medium', 'high', 'HD'];
                    foreach ($sizes as $size) {
                        $dir_ref = $this->destfolder . $this->filetype . "/$size/" . $this->filename . '.mp4';
                        if(file_exists($dir_ref)) {
                            rename($dir_ref, $this->destfolder . $this->filetype . "/$size/" . $this->fileID . '.mp4');
                            if($size == 'high') {
                                $this->repository->updateHigh($this->fileID);
                            }
                        }
                    }
                    return true;
                } else {
                    unlink($media);
                    return $this->error;
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
                    unlink($media);
                    // rename($media, $this->srcfolder . 'source/' . $this->fileID . '.' . $this->extension);
                    $sizes = ['low', 'medium', 'high'];
                    foreach ($sizes as $size) {
                        $dir_ref = $this->srcfolder . "$size/" . $this->filename . '.mp4';
                        if(file_exists($dir_ref)) {
                            rename($dir_ref, $this->srcfolder . "$size/" . $this->fileID . '.mp4');
                        }
                    }
                    return true;
                } else {
                    unlink($media);
                    return $this->error;
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

    public function videoEncoding($video)
    {
        define('base_height', 60);
        $tempfolder = $this->srcfolder;
        if($this->mediatype != 'them') {
            $tempfolder .= 'video/';
        }
        $resolution = $this->getVideoDimensions($video);
        $width = $resolution['width'];
        $height = $resolution['height'];
        $ratio = $width / $height;
        $HD = false;
        $max_size = false;
        $medium_size = false;
        $output_high = '';
        $output_medium = '';
        $output_low = '';

        if($_SESSION['QUICKNET']['login']==='5asec'){
            $old_path = $_SESSION['QUICKNET']['RES_rep'] . 'AUTRES/';
            if($width>$height)$old_path.='VIDEOS HORIZONTALES/';
            else $old_path.='VIDEOS VERTICALES/';
        } else {
            $old_path = $_SESSION['QUICKNET']['RES_rep'] . iconv('UTF-8', 'Windows-1252', 'VIDÉOS') . '/';
        }

        /*
        if($this->extension != 'mp4') {
            exec('ffmpeg -y -i "' . $video . '" -vcodec libx264 -acodec copy -y "' . $tempfolder . 'source/' . $this->filename . '.mp4"');
            unlink($video);
            $video = $tempfolder . 'source/' . $this->filename . '.mp4';
            $this->extension = 'mp4';
        }
        */

        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='plein-écran' || $this->filterDatas['orientation']!=='horizontal' || $this->filterDatas['type']!=='video'){
                        return false;
                    }
                }

                if($width < 1920 && $height < 1080) {
                    $this->error['error'][$this->filename.'.'.$this->extension][] = 'Erreur : Le media doit être uploadé au format minimum 1920 x 1080';
                    return false;
                }

                if ($width > 1920 && $height > 1080) {
                    $max_size = true;
                    $output_high = "1920*1080";
                    $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                }

                if ($width == 1920 && $height == 1080) {
                    //$max_size = false;
                    // On copie simplement la vidéo sans la réencoder dans les répertoires Tizen et LFD!

                    $max_size = false;
                    $output_high = "1920*1080";

                    /*if($this->mediatype != 'them') {
                        copy($video, $this->destfolder . 'video/high/' . $this->filename . '.mp4');
                    } else {
                        copy($video, $this->srcfolder . 'high/' . $this->filename . '.mp4');
                    }

                    if($this->mediatype != 'sync' && $this->mediatype != 'them'){
                        copy($video, $old_path . 'HIGH/' . $this->newFileName . '.mp4');
                    }*/
                }

                if($width >= 1280 && $height >= 720) {
                    $output_medium = "1280*720";
                    $medium_size = true;
                }
                $output_low = "160*90";
                break;

            case 9 / 16:   // Plein Ecran Vertical
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='plein-écran' || $this->filterDatas['orientation']!=='vertical' || $this->filterDatas['type']!=='video'){
                        return false;
                    }
                }

                if($width < 1080 && $height < 1920) {
                    $this->error['error'][$this->filename.'.'.$this->extension][] = 'Erreur : Le media doit être uploadé au format minimum 1080 x 1920';
                    return false;
                }

                if ($width > 1080 && $height > 1920) {
                    $max_size = true;
                    $output_high = "1080*1920";
                    $HD = true;
                }

                if ($width == 1080 && $height == 1920) {
                    //$max_size = false;

                    $max_size = true;
                    $output_high = "1080*1920";

                    copy($video, $this->destfolder . 'video/high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                        copy($video, $old_path . 'HIGH/' . $this->newFileName . '.mp4');
                    }
                }

                if($width >= 720 && $height >= 1280) {
                    $output_medium = "720*1280";
                    $medium_size = true;
                }
                $output_low = "90*160";
                break;

            case 9 / 8:  // Demi Ecran Horizontal
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='demi-écran' || $this->filterDatas['orientation']!=='horizontal' || $this->filterDatas['type']!=='video'){
                        return false;
                    }
                }

                if($width < 1080 && $height < 960) {
                    $this->error['error'][$this->filename.'.'.$this->extension][] = 'Erreur : Le media doit être uploadé au format minimum 1080 x 960';
                    return false;
                }

                if ($width > 1080 && $height > 960) {
                    $max_size = true;
                    $output_high = "1080*960";
                    $HD = true;
                }

                if ($width == 1080 && $height == 960) {
                    //$max_size = false;

                    $max_size = true;
                    $output_high = "1080*960";

                    /*copy($video, $this->destfolder . 'video/high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                      copy($video, $old_path . 'HIGH/' . $this->newFileName . '.mp4');
                    }*/
                }

                if($width >= 720 && $height >= 640) {
                    $medium_size = true;
                    $output_medium = "720*640";
                }
                $output_low = "90*80";
                break;

            case 8 / 9:  // Demi Ecran Vertical
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='demi-écran' || $this->filterDatas['orientation']!=='vertical' || $this->filterDatas['type']!=='video'){
                        return false;
                    }
                }

                if ($width < 960 && $height < 1080) {
                    $this->error['error'][$this->filename.'.'.$this->extension][] = 'Erreur : Le media doit être uploadé au format minimum 960 x 1080';
                    return false;
                }

                if ($width > 960 && $height > 1080) {
                    $max_size = true;
                    $output_high = "960*1080";
                    $HD = true;
                }

                if ($width == 960 && $height == 1080) {
                    //$max_size = false;

                    $max_size = true;
                    $output_high = "960*1080";

                    /*copy($video, $this->destfolder . 'video/high/' . $this->filename . '.mp4');
                    if($this->mediatype != 'sync') {
                      copy($video, $old_path . 'HIGH/' . $this->newFileName . '.mp4');
                    }*/
                }

                if ($width >= 640 && $height >= 720) {
                    $medium_size = true;
                    $output_medium = "640*720";
                }
                $output_low = "80*90";
                break;

            default:
                // case élément graphique
                /*
                if($this->mediatype == 'elmt') {
                    $low_width = round($width/($height/base_height));
                    $medium_width =  round($width/($height/500));
                    if($medium_width%2 == 1) {
                        $medium_width++;
                    }
                    $output_medium = $medium_width . '*500';
                    $medium_size = true;
                    $output_low = $low_width . '*' . base_height;
                }
                */
                // case média diffusable
                if($this->mediatype == 'diff') {
                    return false;
                }
                break;
        }

        $response = [];
        $error = [];

        // file_put_contents(__DIR__ . '/../log/upload.log', 'begin encoding ' . $this->filename . ' || media_type = ' . $this->mediatype . PHP_EOL, FILE_APPEND);

        if ($max_size) {
            // -preset medium, -compression_level, -crf 20 = Constante Rate Factor ??
            // -b:v 20M (pour les dias converties en vidéo) -g 2
            // -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25
            //exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_high . ' -vcodec libx264 -b:v 8M -minrate 1M -maxrate 10M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $tempfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);

            if($height > 1920)
            {

                if($height === 2160)
                {
                    exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset ultrafast -profile:v high -level 4.2 -r 25 -crf 23 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
                }
                if($height === 4320)
                {
                    exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset ultrafast -profile:v high -level 4.2 -r 25 -crf 20 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
                }


            }
            else {
                exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -crf 11 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
            }

// ffmpeg -y -i "C:\Users\cgamb\OneDrive\Documents\Test_18-20.mp4" -s 1920*1080 -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=3:scenecut=0:bframes=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -b:v 15000000 -maxrate 30000000 -bufsize 15000000 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart C:\Users\cgamb\OneDrive\Documents\VideoFFMPEG_150.mp4

//ffmpeg -y -i "C:\Users\cgamb\OneDrive\Documents\Test_18-20.mp4" -s 1920*1080 -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=50:bframes=1 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -crf 1 -coder cabac -flags cgop -open-gop none -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart C:\Users\cgamb\OneDrive\Documents\VideoFFMPEG_150.mp4
//exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -crf 11 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'high/' . $this->filename . '.mp4"', $response['high'], $error['high']);
//dump('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -crf 11 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'high/' . $this->filename . '.mp4"');die;

            if($this->mediatype != 'them') {
                rename($tempfolder . 'high/' . $this->filename . '.mp4', $this->destfolder . 'video/high/' . $this->filename . '.mp4');
                if($this->mediatype != 'sync'){
                    copy( $this->destfolder . 'video/high/' . $this->filename . '.mp4', $old_path . 'HIGH/' . $this->filename . '.mp4');
                }
                if($HD) {

                    // Copie (déplacement impossible à ce stade) de la source vers le dossier HD
                    copy($video, $this->destfolder . 'video/HD/' . $this->newFileName . '.mp4');
                }
            } else {
                rename($tempfolder . 'high/' . $this->filename . '.mp4', $this->srcfolder . 'high/' . $this->newFileName . '.mp4');
            }
        }

        if($medium_size) {

            //exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_medium . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $tempfolder . 'medium/' . $this->filename . '.mp4"', $response['medium'], $error['medium']);

            exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_medium . ' -vf yadif,format=yuv420p -x264-params keyint_min=2:keyint=12:scenecut=80:open-gop=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -crf 17 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'medium/' . $this->filename . '.mp4"', $response['medium'], $error['medium']);

            if($this->mediatype != 'them') {
                rename($tempfolder . 'medium/' . $this->filename . '.mp4', $this->destfolder . 'video/medium/' . $this->filename . '.mp4');
                // Copie du fichier vers l'ancien répertoire correspondant pour les médias diffusables
                if($this->mediatype != 'sync') {
                    copy($this->destfolder . 'video/medium/' . $this->filename . '.mp4', $old_path . $this->newFileName . '.mp4');
                    file_put_contents(__DIR__ . '/../log/upload.log', 'HTTP://La video ' . $this->filename . '.mp4 de format medium et se trouvant dans ' . $this->destfolder . 'video/medium/ a ete recopiee dans le repertoire ' . $old_path . ' avec le nom ' . $this->newFileName . '.mp4' . PHP_EOL, FILE_APPEND);
                }
            }

            // Création de la vignette si au moins le format "medium" existe
            //exec('ffmpeg -y -i "' . $video . '" -r 25 -s ' . $output_low . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $tempfolder . 'low/' . $this->filename . '.mp4"', $response['low'], $error['low']);

            exec('C:\inetpub\wwwroot\ffmpeg\bin\ffmpeg -y -i "' . $video . '" -s ' . $output_low . ' -vf yadif,format=yuv420p -x264-params keyint_min=2:keyint=12:scenecut=40 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -crf 20 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $tempfolder . 'low/' . $this->filename . '.mp4"', $response['low'], $error['low']);

            if($this->mediatype != 'them') {
                rename($tempfolder . 'low/' . $this->filename . '.mp4', $this->destfolder . 'video/low/' . $this->filename . '.mp4');
            }
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

    private function imageResize($img)
    {
        define('base_height', 60);
        // dump($img);
        //dump(getimagesize($img));
        list($width, $height) = getimagesize($img);
        $output = array();
        //dump($width);
        $ratio = $width / $height;
        $source = null;

        if($this->extension == 'png') {
            $source = imagecreatefrompng($img);
        } else {
            $source = imagecreatefromstring(file_get_contents($img));
        }
        //dump($ratio);
        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='plein-écran' || $this->filterDatas['orientation']!=='horizontal' || $this->filterDatas['type']!=='image'){
                        return false;
                    }
                }
                if ($width >= 1920 && $height >= 1080) {
                    $output['high'][0] = 1920;
                    $output['high'][1] = 1080;
                    if ($width > 1920 && $height >1080) {
                        imagepng($source, $this->destfolder . 'image/HD/' . $this->filename . '.png');
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
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='plein-écran' || $this->filterDatas['orientation']!=='vertical' || $this->filterDatas['type']!=='image'){
                        return false;
                    }
                }

                if ($width >= 1080 && $height >= 1920) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 1920;
                    if ($width > 1080 && $height >1920) {
                        imagepng($source, $this->destfolder . 'image/HD/' . $this->filename . '.png');
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

            case 9 / 8:  // Demi Ecran Vertical
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='demi-écran' || $this->filterDatas['orientation']!=='vertical' || $this->filterDatas['type']!=='image'){
                        return false;
                    }
                }

                if ($width >= 1080 && $height >= 960) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 960;
                    if ($width > 1080 && $height > 960) {
                        imagepng($source, $this->destfolder . 'image/HD/' . $this->filename . '.png');
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

            case 8 / 9:  //Demi Ecran Horizontal
                if($this->updated){
                    if(!isset($this->filterDatas) || $this->filterDatas['format']!=='demi-écran' || $this->filterDatas['orientation']!=='horizontal' || $this->filterDatas['type']!=='image'){
                        return false;
                    }
                }

                if ($width >= 960 && $height >= 1080) {
                    $output['high'][0] = 960;
                    $output['high'][1] = 1080;
                    if ($width > 960 && $height > 1080) {
                        imagepng($source, $this->destfolder . 'image/HD/' . $this->filename . '.png');
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
                /*
                if($this->mediatype == 'elmt') {
                    $low_width = round($width/($height/base_height));
                    $output_low = $low_width . '*' . base_height;
                    $output['low'][0] = $output_low;
                    $output['low'][1] = base_height;
                }
                */
                // case média diffusable
                if($this->mediatype == 'diff') {
                    $this->error['error'][$this->filename][]='Erreur : Le media ne comporte pas un ratio réglementaire. Il doit être uploadé au ratio 16/9 ou 9/16';
                    // unlink($img);   [fonction déjà impléméntée si return false]

                    return false; // On exclut l'insertion en base
                }
                break;
        }

        // Correctif 15/06/2020 Skynet ==> Upload format Medium authorized!!
        if($this->mediatype == 'diff' && !isset($output['high'])) {
            $this->error['error'][$this->filename][]='Erreur : La résolution du média est insuffisante. Il doit être uploadé dans la résolution minimale de 1920x1080';
            return false;
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

                imagepng($thumb, $this->destfolder . 'image/' .  $format[$i] . '/' . $this->filename . '.png', 9, PNG_ALL_FILTERS);
                // file_put_contents(__DIR__ . '/../log/resize_img.log', 'OUTPUT ' . $format . ' done!' . PHP_EOL, FILE_APPEND);
                if($i == 0) {
                    // file_put_contents(__DIR__ . '/../log/resize_img.log', 'OUTPUT HIGH COPY OLD DIR!' . PHP_EOL, FILE_APPEND);
                    // Théoriquement, basculer le média avec son nom original dans l'ancien répertoire est suffisant pour qu'il puisse être exploitable dans l'ancienne application. Mais il faut s'assurer, en revanche, que les incrustes soient transposées sous les 2 nominations possibles (id & nom) puisque le répertoire des incrustes est identique pour les 2 applications. [voir si l'on peut créer un repertoire spécifique dans médias pour la newApp]
                    $old_path = $_SESSION['QUICKNET']['RES_rep'] . 'IMAGES/PRODUITS FIXES/PLEIN ECRAN/';
                    /*
                    if ($i == 0) {
                      $old_path .= 'HIGH/';
                    }
                    */
                    copy($this->destfolder . 'image/' .  $format[$i] . '/' . $this->filename . '.png', $old_path . $this->newFileName . '.png');
                    if($_SESSION['QUICKNET']['base']=='cafeteria')copy($this->destfolder . 'image/' .  $format[$i] . '/' . $this->filename . '.png', $_SESSION['QUICKNET']['RES_rep'] . 'IMAGES/MENUBOARD/' . $this->newFileName . '.png');

                    // file_put_contents(__DIR__ . '/../log/upload.log', $old_path . $this->newFileName . '.png' . PHP_EOL, FILE_APPEND);
                }
            }
        }
        return true;
    }

    public function getVideoDimensions($file) {
        $result = ['width' => 'undefined', 'height' => 'undefined'];
        $path_to_json = 'C:/inetpub/wwwroot/tmp/infofile.json'; // Attention à la permission en écriture des utilisateurs dans le dossier tmp!!
        exec('ffprobe -v quiet -print_format json -show_format -show_streams "' . $file . '" > "' . $path_to_json . '"');
        $data = file_get_contents($path_to_json);
        $json = (array) json_decode($data,true);

        // array_filter() inutilisable avec la version de php de l'application!!
        foreach ($json['streams'] as $stream) {
            if(isset($stream['width'])) {
                $result['width'] = $stream['width'];
            }
            if(isset($stream['height'])) {
                $result['height'] = $stream['height'];
            }
        }
        return $result;
    }

    public function retrieveInfo($src)
    {
        $infofile = array();
        $newMedia = new media();
        $newThematic = null;
        $newTemplateContent = new TemplateContents();



        if ($this->filetype == 'video') {
            $newVideo = new video();
            /* Création librairie php-ffmpeg en passant par la ligne de commande pour éviter les problèmes d'incompatibilité */
            $path_to_json = 'C:/inetpub/wwwroot/tmp/infofile.json';

            exec('ffprobe -v quiet -print_format json -show_format -show_streams "' . $src . '" > "' . $path_to_json . '"', $output, $error);
            if (!$error) {
                $datafile = file_get_contents($path_to_json);
                $flux = (array)json_decode($datafile,true);
            } else {
                return false;
            }
            $video = $audio = $format = [];

            foreach($flux['streams'] as $stream) {
                if (isset($stream['codec_type'])) {
                    if($stream['codec_type'] == 'video') {
                        $video = $stream;
                    }
                    if($stream['codec_type'] == 'audio') {
                        $audio = $stream;
                    }
                }
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
                $file = substr($file, 4); // On retire le 'tmp_' généré par le process de l'upload

                if ($this->mediatype != 'them') {

                    $newTemplateContent->setName($file);
                    $newTemplateContent->setContentType('video');

                    if(!$this->updated) {
                        $this->fileID=$this->repository->insert($newTemplateContent);
                        $templateContentId = $this->fileID;

                    }else{
                        $this->repository->update($newTemplateContent, 'image');
                    }


                    $newMedia->setType($this->mediatype);
                    $newMedia->setSize(round($data['size'] / (1024 * 1024), 2) . ' Mo');
                    $newMedia->setDate(date('Y-m-d')); // substr($data['creation_time'], 0, 10)
                    $newMedia->setId($this->fileID); // substr($data['creation_time'], 0, 10)
                    if(!$this->updated) {
                        $newMedia->setFilename($file);
                        $this->fileID = $this->repository->insert($newMedia,$this->fileID);
                    }else{
                        $newMedia->setId($this->fileID);
                        $this->repository->update($newMedia);
                    }
                    $newVideo->setMedia($this->fileID);
                    $newVideo->setId($templateContentId);
                    $newVideo->setExtension($ext);
                    $newVideo->setFormat($data['major_brand']);
                    $newVideo->setRatio($ratio);
                    $newVideo->setHeight($data['height']);
                    $newVideo->setWidth($data['width']);
                    $newVideo->setSampleSize($data['bits_per_raw_sample'] . ' bits');
                    $newVideo->setEncoder($data['encoder']);   // format_long_name ??? Quicktime!
                    $newVideo->setVideoCodec($data['codec_long_name']);
                    $newVideo->setVideoCodecLevel($level);
                    $newVideo->setVideoFrequence(substr($data['avg_frame_rate'], 0, -2) . ' img/s');
                    $newVideo->setVideoFrames($data['nb_frames']);
                    $newVideo->setVideoDebit((int)($data['bit_rate'] / 1000) . ' kbit/s');
                    $newVideo->setDuration(round($data['duration'], 2) . ' secondes');
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
                        $newVideo->setAudioFrames($data['nb_frames']);
                    } else {
                        $newThematic['audioCodec'] = $data['codec_long_name'];
                        $newThematic['audioDebit'] = (int)($data['bit_rate'] / 1000) . ' kbit/s';
                        $newThematic['audioFrequence'] = $data['sample_rate'] . ' Hz';
                        $newThematic['audioChannel'] = $data['channels'];
                        $newThematic['audioFrames'] = $data['nb_frames'];
                    }
                }
                if ($this->mediatype != 'them') {
                    if(!$this->updated) {
                        $this->repository->insert($newVideo,$this->fileID);
                    }else{
                        $this->repository->update($newVideo);
                    }
//                    $this->error = $this->repository->insert($newVideo,'video');
                } else {
                    $th_rep = new theme_rep();
                    $this->fileID = $th_rep->saveVideoThematic($newThematic);
                }
            }
        }

        /* Instanciation de la classe image */
        if($this->filetype == 'image') {
            $this->extension = 'png' ;
            $newImg = new image();
            $templateContentId = false;
            $newTemplateContent = new TemplateContents();


            $newTemplateContent->setName($this->filename);
            $newTemplateContent->setContentType('image');

            if(!$this->updated) {
                $this->fileID=$this->repository->insert($newTemplateContent);
                $templateContentId = $this->fileID;

            }else{
                $this->repository->update($newTemplateContent, 'image');
            }

            list($width, $height) = getimagesize($src);
            $ratio = $this->EstablishFormat($width, $height);
            $newMedia->setType($this->mediatype);
            $newMedia->setId($this->fileID);
            $newMedia->setSize(round(filesize($src)/(1024*1024), 2) . ' Mo');
            $newMedia->setDate(date('Y-m-d')); // substr($data['creation_time'], 0, 10)
            // dump($newMedia);
            if(!$this->updated) {
                $newMedia->setFilename($this->filename);

                $this->fileID = $this->repository->insert($newMedia,$this->fileID);
            }else{
                $newMedia->setId($this->fileID);
                $this->repository->update($newMedia);

            }



            $newImg->setMedia($this->fileID);
            $newImg->setExtension($this->extension);
            $newImg->setRatio($ratio);
            $newImg->setHeight($height);
            $newImg->setWidth($width);
            $newImg->setId($templateContentId);
//            dump($templateContentId);
//            $newImg->setId($templateContentId);
            if(!$this->updated) {

                $this->repository->insert($newImg,$this->fileID);

            }else{
                $this->repository->update($newImg, 'image');
            }
            //$this->error = $success;
        }
    }

    public function EstablishFormat($width, $height) {
        $ratio = $width / $height;

        switch ($ratio) {
            case 16 / 9:
                $format = 'plein-écran horizontal (16/9)';
                break;
            case 9 / 16:
                $format = 'plein-écran vertical (9/16)';
                break;
            case 9 / 8;
                $format = 'demi-écran vertical (9/8)';     // modif
                break;
            case 8 / 9:
                $format = 'demi-écran horizontal (8/9)';       // modif
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
        $path_to_warehouse = $this->destfolder . 'frame/' . $filename;
        $cmd = 'ffmpeg -ss ' . $time . ' -i "' . $file . '" -t 1 -f image2 "' . $path_to_warehouse . '"';
        exec($cmd, $output, $error);
    }

    public function get_dump() {
        return $this->error;
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

}
