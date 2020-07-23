<?php


namespace App\Service;


use App\Entity\Customer\ElementGraphic;
use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\Video;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaEncodeManager
{

    private ManagerRegistry $__managerRegistry;

    private ParameterBagInterface $__parameterBag;

    private string $__mediasSourceFolder;

    private string $__mediasEncodeOutputFolder;
    
    private string $__mediaOrientation = "";

    private array $__errors = [];

    private MediasHandler $__mediaHandler;

    private array $__acceptedRatios= [ 16/9, 9/16, 9/8, 8/9 ];

    private array $__encodeOutputSizesFolders = [ 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'HD' => 'HD', '4k' => 'UHD-4k', '8k' => 'UHD-8k' ];

    private array $__filesToRenameWithId = [];

    public function __construct(ManagerRegistry $managerRegistry, ParameterBagInterface $parameterBag)
    {

        $this->__managerRegistry = $managerRegistry;
        $this->__parameterBag = $parameterBag;
        $this->__mediasSourceFolder = $parameterBag->get('project_dir') . '/../upload/source';
        $this->__mediasEncodeOutputFolder = $parameterBag->get('project_dir') . '/../upload/medias_encode';
        $this->__mediaHandler = new MediasHandler($parameterBag);

    }

    public function getEncodeErrors()
    {
        return $this->__errors;
    }

    public function encodeMedia(array $mediaInfos)
    {

        // fileType = image; video
        // mediaType = diff; them; sync; elmnt

        $this->__mediasSourceFolder .= '/' . $mediaInfos['customerName'] . '/video/' . $mediaInfos['mediaType'];
        $this->__mediasEncodeOutputFolder .= '/' . $mediaInfos['customerName'] . '/video';

        if(!file_exists($this->__mediasSourceFolder))
            mkdir($this->__mediasSourceFolder, 0777, true);

        if(!file_exists($this->__mediasEncodeOutputFolder))
            mkdir($this->__mediasEncodeOutputFolder, 0777, true);

        $mediaSource = $this->__mediasSourceFolder . '/' . $mediaInfos['fileName'] . '.' . $mediaInfos['extension'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $mediaSource);
        $splash = explode('/', $mimeType);
        $fileType = $splash[0];
        $real_file_extension = $splash[1];

        if(!file_exists($mediaSource))
        {
            $this->__errors[] = "source file not found";
            return false;
        }

        else if(!in_array($real_file_extension, $this->__parameterBag->get("authorizedExtensions")))
        {
            $this->__errors[] = "bad extension($real_file_extension)";
            return false;
        }

        else
        {

            list($width, $height) = $this->__mediaHandler->getVideoDimensions($mediaSource);

            $ratio = $width/$height;

            if(!in_array($ratio, $this->__acceptedRatios))
            {
                $this->__errors[] = "bad ratio($width/$height)";
                return false;
            }
            else
            {

                if($this->encodeVideo($mediaSource, ['fileName' => $mediaInfos['fileName'], 'ratio'=> $ratio, 'width' => $width, 'height' => $height, 'mediaType' => $mediaInfos['mediaType']]))
                {

                    $entityManager = $this->__managerRegistry->getManager(strtolower($mediaInfos['customerName']));
                    $mediaRep = $entityManager->getRepository(Media::class)->setEntityManager($entityManager);

                    //'fileName' => $taskMediaInfo['name'],
                    //                        'customerName' => $customer_name,
                    //                        'mediaType' => $task->getMediatype(),
                    //                        'uploadDate' => $taskMediaInfo['createdAt'],
                    //                        'extension' => $taskMediaInfo['extension'],
                    //                        'mediaProducts' => $taskMediaInfo['products'],
                    //                        'mediaTags' => $taskMediaInfo['tags'],
                    //                        'mimeType' => $taskMediaInfo['mimeType'],

                    switch ($mediaInfos['mediaType']) {

                        case "them":
                            break;

                        case "elmt":
                            $media = new ElementGraphic();
                            break;

                        case "sync":
                            break;

                        case "diff":

                            if($fileType ==='image')
                            {

                                $size =  ( round(filesize($mediaSource)/(1024*1024), 2) > 0.00) ? round(filesize($mediaSource)/(1024*1024), 2) . ' Mo' : round(filesize($mediaSource), 2) . ' o';

                                list($width, $height) = getimagesize($mediaSource);

                                $media = new Image();
                                $media->setSize($size);

                            }
                            else
                            {

                                $media = new Video();
                                $media->setSize(round($data['size'] / (1024 * 1024), 2) . ' Mo')
                                      ->setFormat($data['major_brand'])
                                      ->setSampleSize($data['bits_per_raw_sample'] . ' bits')
                                      ->setEncoder($data['encoder'])
                                      ->setVideoCodec($data['codec_long_name'])
                                      ->setVideoCodecLevel($level)
                                      ->setVideoFrequence(substr($data['avg_frame_rate'], 0, -2) . ' img/s')
                                      ->setVideoFrame($data['nb_frames'])
                                      ->setVideoDebit((int)($data['bit_rate'] / 1000) . ' kbit/s')
                                      ->setDuration(round($data['duration'], 2) . ' secondes');

                            }

                            $media->setMediaType('diff')
                                  ->setWidth($width)
                                  ->setHeight($height)
                                  ->setIsArchived(false)
                                  ->setContainIncruste(false)
                                  ->setName($mediaInfos['fileName'])
                                  ->setOrientation($this->__mediaOrientation)
                                  ->setMimeType($mediaInfos['mimeType'])
                                  ->setRatio($ratio)
                                  ->setExtension($mediaInfos['extension'])
                                  ->setCreatedAt(new DateTime())
                                  ->setDiffusionStart( new DateTime() )
                                  ->setDiffusionEnd( (new DateTime())->modify('+10 year') );

                            break;

                        default:
                            throw new Exception( sprintf("Unreconginzed mediaType : '%s'", $mediaInfos['mediaType']) );

                    }

                    $entityManager->persist($media);
                    $entityManager->flush();

                    $this->renameMediaWithId($media->getId());

                    return true;
                }

                return false;
            }

        }

    }

    private function encodeVideo($videoPath, $videoInfos)
    {

        switch ($videoInfos['ratio'])
        {

            case 16 / 9:  // Plein Ecran Horizontal

                if($videoInfos['width'] < 1920 && $videoInfos['height'] < 1080) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1920 x 1080';
                    return false;
                }

                if ($videoInfos['width'] > 1920 && $videoInfos['width'] > 1080) {
                    $max_size = true;
                    $output_high = "1920*1080";
                    $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                }

                if ($videoInfos['width'] == 1920 && $videoInfos['height'] == 1080) {
                    $max_size = false;
                    // On copie simplement la vidéo sans la réencoder dans les répertoires Tizen et LFD!
                    copy($videoPath, $this->__mediasEncodeOutputFolder . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');



                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 1280 && $videoInfos['height'] >= 720) {
                    $output_medium = "1280*720";
                    $medium_size = true;
                }
                $output_low = "160*90";

                $this->__mediaOrientation = 'Horizontal';

                break;

            case 9 / 16:   // Plein Ecran Vertical

                if($videoInfos['width'] < 1080 && $videoInfos['height'] < 1920) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1080 x 1920';
                    return false;
                }

                if ($videoInfos['width'] > 1080 && $videoInfos['height'] > 1920) {
                    $max_size = true;
                    $output_high = "1080*1920";
                    $HD = true;
                }

                if ($videoInfos['width'] == 1080 && $videoInfos['height'] == 1920) {
                    $max_size = false;
                    copy($videoPath, $this->__mediasEncodeOutputFolder . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');
                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 1280) {
                    $output_medium = "720*1280";
                    $medium_size = true;
                }
                $output_low = "90*160";

                $this->__mediaOrientation = 'Vertical';

                break;

            case 9 / 8:  // Demi Ecran Horizontal

                if($videoInfos['width'] < 1080 && $videoInfos['height'] < 960) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1080 x 960';
                    return false;
                }

                if ($videoInfos['width'] > 1080 && $videoInfos['height'] > 960) {
                    $max_size = true;
                    $output_high = "1080*960";
                    $HD = true;
                }

                if ($videoInfos['width'] == 1080 && $videoInfos['height'] == 960) {
                    $max_size = false;
                    copy($videoPath, $this->__mediasEncodeOutputFolder . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');
                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 640) {
                    $medium_size = true;
                    $output_medium = "720*640";
                }
                $output_low = "90*80";

                $this->__mediaOrientation = 'Horizontal';

                break;

            case 8 / 9:  // Demi Ecran Vertical

                if ($videoInfos['width'] < 960 && $videoInfos['height'] < 1080) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 960 x 1080';
                    return false;
                }

                if ($videoInfos['width'] > 960 && $videoInfos['height'] > 1080) {
                    $max_size = true;
                    $output_high = "960*1080";
                    $HD = true;
                }

                if ($videoInfos['width'] == 960 && $videoInfos['height'] == 1080) {
                    $max_size = false;
                    copy($videoPath, $this->__mediasEncodeOutputFolder . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');
                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if ($videoInfos['width'] >= 640 && $videoInfos['height'] >= 720) {
                    $medium_size = true;
                    $output_medium = "640*720";
                }
                $output_low = "80*90";

                $this->__mediaOrientation = 'Vertical';

                break;

        }

        if ($max_size) {

            $copyFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'];

            if($videoInfos['height'] === 2160) // 4k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['4k'];
                //$this->__mediasEncodeOutputFolder .= $this->__encodeOutputSizesFolders['4k'];

            /*else if($videoInfos['height'] === 4320) // 8k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['8k'];
                //$this->__mediasEncodeOutputFolder .= $this->__encodeOutputSizesFolders['8k'];
            */

            else $copyFolder = $this->__mediasEncodeOutputFolder . $this->__encodeOutputSizesFolders['high'];

            if(!file_exists($copyFolder))
                mkdir($copyFolder, 0777, true);

            // -preset medium, -compression_level, -crf 20 = Constante Rate Factor ??
            // -b:v 20M (pour les dias converties en vidéo) -g 2
            exec('ffmpeg -y -i "' . $videoPath . '" -r 25 -s ' . $output_high . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -acodec copy -y "' . $copyFolder . '/' . $videoInfos['fileName'] . '.mp4"', $cmdResultStatus);

            if($cmdResultStatus === 1)
                throw new Exception("Erreur lors de l'encodage du média en high");

            $this->__filesToRenameWithId[] = $copyFolder . '/' . $videoInfos['fileName'] . '.mp4';

            if($videoInfos['mediaType'] != 'them') {

                if($HD) {

                    $copyFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'];

                    if($videoInfos['height'] === 2160) // 4k
                        $copyFolder .= '/' . $this->__encodeOutputSizesFolders['4k'];

                    /*else if($videoInfos['height'] === 4320) // 8k
                        $copyFolder .= '/' . $this->__encodeOutputSizesFolders['8k'];
                    */

                    if(!file_exists($copyFolder))
                        mkdir($copyFolder, 0777, true);

                    copy($videoPath, $copyFolder . '/' . $videoInfos['fileName'] . '.mp4');

                    $this->__filesToRenameWithId[] = $copyFolder . '/' . $videoInfos['fileName'] . '.mp4';
                }

            }
        }

        if($medium_size) {

            if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium']))
                mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'], 0777, true);

            if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low']))
                mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'], 0777, true);

            exec('ffmpeg -y -i "' . $videoPath . '" -r 25 -s ' . $output_medium . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['fileName'] . '.mp4"', $cmdResultStatus);

            if($cmdResultStatus === 1)
                throw new Exception("Erreur lors de l'encodage du média en medium");

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['fileName'] . '.mp4';

            // Création de la vignette si au moins le format "medium" existe
            exec('ffmpeg -y -i "' . $videoPath . '" -r 25 -s ' . $output_low . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -g 250 -bf 2 -b_strategy 0 -sc_threshold 0 -keyint_min 25 -acodec copy -y "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['fileName'] . '.mp4"', $cmdResultStatus);

            if($cmdResultStatus === 1)
                throw new Exception("Erreur lors de l'encodage du média en low");

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['fileName'] . '.mp4';

        }
        
        return true;

    }

    private function insertMedia()
    {

    }

    private function renameMediaWithId($mediaId)
    {

        /*if(file_exists($path))
        {

            rename($path, $root . '/' . $folderName . $mediaName . '.mp4');

            if($this->filetype === 'image')
            {
                $mediasHandler->changeImageDpi($dest, $dest, 72);
                $mediasHandler->convertImageCMYKToRGB($dest, $dest);
            }

        }*/

    }

}