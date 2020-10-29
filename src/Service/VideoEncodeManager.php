<?php


namespace App\Service;


use App\Entity\Customer\ElementGraphic;
use App\Entity\Customer\Image;
use App\Entity\Customer\Media;
use App\Entity\Customer\Video;
use App\Entity\Customer\VideoElementGraphic;
use App\Entity\Customer\VideoThematic;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VideoEncodeManager
{

    private ParameterBagInterface $__parameterBag;

    private string $__mediasSourceFolder;

    private string $__mediasEncodeOutputFolder;
    
    private string $__orientation = "";

    private array $__errors = [];

    private MediaInfosHandler $__mediaHandler;

    private array $__acceptedRatios= [ 16/9, 9/16, 9/8, 8/9 ];

    private array $__encodeOutputSizesFolders = [ 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'HD' => 'HD', '4k' => 'UHD-4k', '8k' => 'UHD-8k' ];

    private array $__videoInfos = [];

    private array $__filesToRenameWithId = [];

    private array $__currentMediaOutputFormat = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {

        $this->__parameterBag = $parameterBag;
        $this->__mediasSourceFolder = $parameterBag->get('project_dir') . '/../upload/source';
        $this->__mediasEncodeOutputFolder = $parameterBag->get('project_dir') . '/../upload/medias';
        $this->__mediaHandler = new MediaInfosHandler($parameterBag);

    }

    public function getEncodeErrors()
    {
        return $this->__errors;
    }

    public function getFilesToRenameList()
    {
        return $this->__filesToRenameWithId;
    }

    public function getEncodedVideoInfos()
    {
        return $this->__videoInfos;
    }

    public function encodeMedia(array $videoInfos)
    {

        $this->__mediasSourceFolder .= '/' . $videoInfos['customerName'] . '/video/' . $videoInfos['mediaType'];
        $this->__mediasEncodeOutputFolder .= '/' . $videoInfos['customerName'] . '/video/' . $videoInfos['mediaType'];

        if($videoInfos['height'] === 2160)
            $this->__mediasSourceFolder .= '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $this->__encodeOutputSizesFolders['4k'];

        else if($videoInfos['height'] === 4320)
            $this->__mediasSourceFolder .= '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $this->__encodeOutputSizesFolders['8k'];

        if(!file_exists($this->__mediasSourceFolder))
            mkdir($this->__mediasSourceFolder, 0777, true);

        if(!file_exists($this->__mediasEncodeOutputFolder))
            mkdir($this->__mediasEncodeOutputFolder, 0777, true);

        $mediaSource = $this->__mediasSourceFolder . '/' . $videoInfos['name'] . '.' . $videoInfos['extension'];


        if(!file_exists($mediaSource))
        {
            $this->__errors[] = "source file not found";
            return false;
        }

        else if(!in_array($videoInfos['extension'], $this->__parameterBag->get("authorizedExtensions")))
        {
            $this->__errors[] = "bad extension(" . $videoInfos['extension'] . ")";
            return false;
        }

        else
        {

            /*list($width, $height) = $this->__mediaHandler->getVideoDimensions($mediaSource);

            $ratio = $width/$height;*/

            if(!in_array($videoInfos['width'] / $videoInfos['height'], $this->__acceptedRatios))
            {
                $this->__errors[] = "bad ratio(" . $videoInfos['width'] . "/" . $videoInfos['height'] . ")";
                return false;
            }
            else
            {

                if($this->encodeVideo($mediaSource, $videoInfos))
                {

                    $videoInfos['orientation'] = $this->__orientation;
                    $this->__videoInfos = $videoInfos;

                    /*if($videoInfos['mediaType'] === 'sync')
                    {
                        //$this->__videoInfos['synchros'] = $videoInfos['synchros'];
                        $this->__videoInfos['position'] = $videoInfos['position'];
                    }*/

                    /*$this->__videoInfos['fileType'] = "video";

                    // on écrit les infos du media dans un json
                    // les infos seront recupérés plus tard après la caractérisation du media puis seront supprimé du fichier
                    $mediaToInsertDatasHandler = new MediaToInsertDatasHandler($this->__parameterBag);

                    $mediaToInsertDatasHandler->saveMediaInfosInJson($this->__videoInfos );*/

                    return true;
                }

                return false;
            }

        }

    }

    private function encodeVideo(string $videoPath, array $videoInfos)
    {

        switch ($videoInfos['width'] / $videoInfos['height'])
        {

            case 16/9:  // Plein Ecran Horizontal

                if($videoInfos['width'] < 1920 && $videoInfos['height'] < 1080) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1920 x 1080';
                    return false;
                }

                if ($videoInfos['width'] > 1920 && $videoInfos['height'] > 1080) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    if($videoInfos['height'] === 2160) // 4k
                    {
                        $this->__currentMediaOutputFormat[] = '4k';
                    }

                    else if($videoInfos['height'] === 4320) // 8k
                    {
                        $this->__currentMediaOutputFormat[] = '8k';
                    }

                    $max_size = true;
                    $output_high = "1920*1080";
                    $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                }

                if ($videoInfos['width'] === 1920 && $videoInfos['height'] === 1080) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "1920*1080";

                    if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high']))
                        mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'], 0777, true);

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['name'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['name'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 1280 && $videoInfos['height'] >= 720) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output_medium = "1280*720";
                    $medium_size = true;
                }

                $this->__currentMediaOutputFormat[] = 'low';

                $output_low = "160*90";

                $this->__orientation = 'Horizontal';

                break;

            case 9/16:   // Plein Ecran Vertical

                if($videoInfos['width'] < 1080 && $videoInfos['height'] < 1920) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1080 x 1920';
                    return false;
                }

                if ($videoInfos['width'] > 1080 && $videoInfos['height'] > 1920) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    if($videoInfos['height'] === 2160) // 4k
                    {
                        $this->__currentMediaOutputFormat[] = '4k';
                    }

                    else if($videoInfos['height'] === 4320) // 8k
                    {
                        $this->__currentMediaOutputFormat[] = '8k';
                    }

                    $max_size = true;
                    $output_high = "1080*1920";
                    $HD = true;
                }

                if ($videoInfos['width'] == 1080 && $videoInfos['height'] == 1920) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "1080*1920";

                    if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high']))
                        mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'], 0777, true);

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['name'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['name'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 1280) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output_medium = "720*1280";
                    $medium_size = true;
                }

                $this->__currentMediaOutputFormat[] = 'low';

                $output_low = "90*160";

                $this->__orientation = 'Vertical';

                break;

            case 9/8:  // Demi Ecran Horizontal

                if($videoInfos['width'] < 1080 && $videoInfos['height'] < 960) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1080 x 960';
                    return false;
                }

                if ($videoInfos['width'] > 1080 && $videoInfos['height'] > 960) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "1080*960";
                    $HD = true;
                }

                if ($videoInfos['width'] == 1080 && $videoInfos['height'] == 960) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "1080*960";

                    if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high']))
                        mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'], 0777, true);

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['name'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['name'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 640) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $medium_size = true;
                    $output_medium = "720*640";
                }

                $this->__currentMediaOutputFormat[] = 'low';

                $output_low = "90*80";

                $this->__orientation = 'Horizontal';

                break;

            case 8/9:  // Demi Ecran Vertical

                if ($videoInfos['width'] < 960 && $videoInfos['height'] < 1080) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 960 x 1080';
                    return false;
                }

                if ($videoInfos['width'] > 960 && $videoInfos['height'] > 1080) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "960*1080";
                    $HD = true;
                }

                if ($videoInfos['width'] == 960 && $videoInfos['height'] == 1080) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $max_size = true;
                    $output_high = "960*1080";

                    if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high']))
                        mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'], 0777, true);

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['name'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['name'] . '.mp4');
                    }*/
                }

                if ($videoInfos['width'] >= 640 && $videoInfos['height'] >= 720) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $medium_size = true;
                    $output_medium = "640*720";
                }

                $this->__currentMediaOutputFormat[] = 'low';

                $output_low = "80*90";

                $this->__orientation = 'Vertical';

                break;

        }

        if (isset($max_size) && $max_size) {

            $copyFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'];

            /*if($videoInfos['height'] === 2160) // 4k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['4k'];*/

            /*else if($videoInfos['height'] === 4320) // 8k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['8k'];*/

            if(!file_exists($copyFolder))
                mkdir($copyFolder, 0777, true);


            if($videoInfos['height'] > 1920)
            {

                if($videoInfos['height'] === 2160)
                    exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset ultrafast -profile:v high -level 4.2 -r 25 -crf 23 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $copyFolder . '/' . $videoInfos['name'] . '.mp4"', $output, $cmdResultStatus);

                if($videoInfos['height'] === 4320)
                    exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset ultrafast -profile:v high -level 4.2 -r 25 -crf 20 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $copyFolder . '/' . $videoInfos['name'] . '.mp4"', $output, $cmdResultStatus);

            }

            else
                exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=6:scenecut=80:bframes=0:open-gop=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -crf 11 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $copyFolder . '/' . $videoInfos['name'] . '.mp4"', $output, $cmdResultStatus);

            if($cmdResultStatus === 1)
            {
                dump($output); die;
                throw new Exception("Erreur lors de l'encodage du média en high");
            }

            $this->__filesToRenameWithId[] = $copyFolder . '/' . $videoInfos['name'] . '.mp4';

            if($videoInfos['mediaType'] != 'them') {

                if(isset($HD) and $HD) {

                    $copyFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'];

                    if($videoInfos['height'] === 2160) // 4k
                        $copyFolder .= '/' . $this->__encodeOutputSizesFolders['4k'];

                    else if($videoInfos['height'] === 4320) // 8k
                        $copyFolder .= '/' . $this->__encodeOutputSizesFolders['8k'];

                    if(!file_exists($copyFolder))
                        mkdir($copyFolder, 0777, true);

                    copy($videoPath, $copyFolder . '/' . $videoInfos['name'] . '.mp4');

                    $this->__filesToRenameWithId[] = $copyFolder . '/' . $videoInfos['name'] . '.mp4';
                }

            }
        }

        if(isset($medium_size) && $medium_size) {

            if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium']))
                mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'], 0777, true);

            if(!file_exists($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low']))
                mkdir($this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'], 0777, true);

            exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_medium . ' -vf yadif,format=yuv420p -x264-params keyint_min=2:keyint=12:scenecut=80:open-gop=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -crf 17 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['name'] . '.mp4"', $output, $cmdResultStatus);

            //exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_medium . ' -vf yadif,format=yuv420p -loglevel debug -x264-params keyint_min=2:keyint=9:scenecut=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -b:v 4000000 -maxrate 8000000 -bufsize 4000000 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart  "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['name'] . '.mp4"', $output,$cmdResultStatus);

            if($cmdResultStatus !== 0)
            {
                dump($output);die;
                throw new Exception("Erreur lors de l'encodage du média en medium");
            }

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['name'] . '.mp4';

            // Création de la vignette si au moins le format "medium" existe
            //exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_low . ' -vf yadif,format=yuv420p -loglevel debug -x264-params keyint_min=2:keyint=9:scenecut=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -b:v 1000000 -maxrate 2000000 -bufsize 1000000 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart  "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['name'] . '.mp4"', $output,$cmdResultStatus);

            exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_low . ' -vf yadif,format=yuv420p -x264-params keyint_min=2:keyint=12:scenecut=40 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -crf 20 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['name'] . '.mp4"', $output,$cmdResultStatus);

            if($cmdResultStatus !== 0)
            {
                dump($output);die;
                throw new Exception("Erreur lors de l'encodage du média en low");
            }

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['name'] . '.mp4';

        }

        // si le media n'a pas été encodé dans tout les formats retourner false
        return $this->allMediaOutputFormatsIsCreated($videoInfos);


    }

    private function allMediaOutputFormatsIsCreated(array $videoInfos): bool
    {

        $HDFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . ( (in_array('4k', $this->__currentMediaOutputFormat)) ? $this->__encodeOutputSizesFolders['4k'] : ((in_array('8k', $this->__currentMediaOutputFormat)) ? $this->__encodeOutputSizesFolders['8k'] : "") );
        $highFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'];
        $mediumForlder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'];
        $lowFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'];

        if( in_array('4k', $this->__currentMediaOutputFormat) && !file_exists($HDFolder . '/' . $videoInfos['name']. ".mp4") )
        {
            $this->__errors[] = "Incomplete encodage (Missing HD/4K format)";
            return false;
        }
        else if( in_array('8k', $this->__currentMediaOutputFormat) && !file_exists($HDFolder . '/' . $videoInfos['name']. ".mp4") )
        {
            $this->__errors[] = "Incomplete encodage (Missing HD/8K format)";
            return false;
        }
        else if( in_array('high', $this->__currentMediaOutputFormat) && !file_exists($highFolder . '/' . $videoInfos['name']. ".mp4") )
        {
            $this->__errors[] = "Incomplete encodage (Missing high format)";
            return false;
        }
        else if( in_array('medium', $this->__currentMediaOutputFormat) && !file_exists($mediumForlder . '/' . $videoInfos['name']. ".mp4") )
        {
            $this->__errors[] = "Incomplete encodage (Missing medium format)";
            return false;
        }
        else if( in_array('low', $this->__currentMediaOutputFormat) && !file_exists($lowFolder . '/' . $videoInfos['name']. ".mp4") )
        {
            $this->__errors[] = "Incomplete encodage (Missing low format)";
            return false;
        }
        else
            return true;

    }

    public function renameMediaWithId(string $mediaName, int $mediaId)
    {

        foreach ($this->__filesToRenameWithId as $filesToRenameWithIdPath)
        {

            $path = str_replace($mediaName, $mediaId, $filesToRenameWithIdPath);

            if(!file_exists($filesToRenameWithIdPath))
                throw new Exception(sprintf("File not found : %s", $filesToRenameWithIdPath));

            rename($filesToRenameWithIdPath, $path);

        }

    }

}