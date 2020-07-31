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

    private MediasHandler $__mediaHandler;

    private array $__acceptedRatios= [ 16/9, 9/16, 9/8, 8/9 ];

    private array $__encodeOutputSizesFolders = [ 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'HD' => 'HD', '4k' => 'UHD-4k', '8k' => 'UHD-8k' ];

    private array $__videoInfos = [];

    private array $__filesToRenameWithId = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {

        $this->__parameterBag = $parameterBag;
        $this->__mediasSourceFolder = $parameterBag->get('project_dir') . '/../upload/source';
        $this->__mediasEncodeOutputFolder = $parameterBag->get('project_dir') . '/../upload/medias_encode';
        $this->__mediaHandler = new MediasHandler($parameterBag);

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

        if(!file_exists($this->__mediasSourceFolder))
            mkdir($this->__mediasSourceFolder, 0777, true);

        if(!file_exists($this->__mediasEncodeOutputFolder))
            mkdir($this->__mediasEncodeOutputFolder, 0777, true);

        $mediaSource = $this->__mediasSourceFolder . '/' . $videoInfos['fileName'] . '.' . $videoInfos['extension'];

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

                if($this->encodeVideo($mediaSource, ['fileName' => $videoInfos['fileName'], 'ratio'=> $ratio, 'width' => $width, 'height' => $height, 'mediaType' => $videoInfos['mediaType']]))
                {

                    $data = $this->getVideoFileCharacteristics($mediaSource);
                    $audioCharacteristics = null;

                    $videoCharacteristics = $data['video'];

                    if(array_key_exists('audio',$data))
                        $audioCharacteristics = $data['audio'];

                    $level_array = str_split($videoCharacteristics['level']);
                    $level = implode('.', $level_array);

                    $this->__videoInfos = [
                        'size' => round($videoCharacteristics['size'] / (1024 * 1024), 2) . ' Mo',
                        'format' => $videoCharacteristics['major_brand'],
                        'sampleSize' => $videoCharacteristics['bits_per_raw_sample'] . ' bits',
                        'encoder' => $videoCharacteristics['encoder'],
                        'videoCodec' => $videoCharacteristics['codec_long_name'],
                        'videoCodecLevel' => $level,
                        'videoFrequence' => substr($videoCharacteristics['avg_frame_rate'], 0, -2) . ' img/s',
                        'videoFrame' => $videoCharacteristics['nb_frames'],
                        'videoDebit' => (int)($videoCharacteristics['bit_rate'] / 1000) . ' kbit/s',
                        'duration' => round($videoCharacteristics['duration'], 2) . ' secondes',
                        'mediaType' => $videoInfos['mediaType'],
                        'width' => $width,
                        'height' => $height,
                        'createdAt' => $videoInfos['createdAt'],
                        'fileName' => $videoInfos['fileName'],
                        'orientation' => $this->__orientation,
                        'mimeType' => $videoInfos['mimeType'],
                        'ratio' => "$width/$height",
                        'extension' => $videoInfos['extension'],
                        'audioCodec' => ($audioCharacteristics !== null) ? $audioCharacteristics['codec_long_name'] : null,
                        'audioDebit' => ($audioCharacteristics !== null) ? (int)($audioCharacteristics['bit_rate'] / 1000) . ' kbit/s' : null,
                        'audioFrequence' => ($audioCharacteristics !== null) ? $audioCharacteristics['sample_rate'] . ' Hz' : null,
                        'audioChannel' => ($audioCharacteristics !== null) ? $audioCharacteristics['channels'] : null,
                        'audioFrame' => ($audioCharacteristics !== null) ? $audioCharacteristics['nb_frames'] : null,
                    ];

                    if($videoInfos['mediaType'] === 'sync')
                    {
                        $this->__videoInfos['synchros'] = $videoInfos['synchros'];
                        $this->__videoInfos['position'] = $videoInfos['position'];
                    }

                    return true;
                }

                return false;
            }

        }

    }

    private function encodeVideo(string $videoPath, array $videoInfos)
    {

        switch ($videoInfos['ratio'])
        {

            case 16/9:  // Plein Ecran Horizontal

                if($videoInfos['width'] < 1920 && $videoInfos['height'] < 1080) {
                    $this->__errors[] = 'permission denied - bad resolution - format minimum: 1920 x 1080';
                    return false;
                }

                if ($videoInfos['width'] > 1920 && $videoInfos['height'] > 1080) {
                    $max_size = true;
                    $output_high = "1920*1080";
                    $HD = true; // On se prépare à copier la source dans 'HD' si la résolution dépasse le format high
                }

                if ($videoInfos['width'] == 1920 && $videoInfos['height'] == 1080) {
                    $max_size = true;
                    $output_high = "1920*1080";

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 1280 && $videoInfos['height'] >= 720) {
                    $output_medium = "1280*720";
                    $medium_size = true;
                }
                $output_low = "160*90";

                $this->__orientation = 'Horizontal';

                break;

            case 9/16:   // Plein Ecran Vertical

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
                    $max_size = true;
                    $output_high = "1080*1920";

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 1280) {
                    $output_medium = "720*1280";
                    $medium_size = true;
                }
                $output_low = "90*160";

                $this->__orientation = 'Vertical';

                break;

            case 9/8:  // Demi Ecran Horizontal

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
                    $max_size = true;
                    $output_high = "1080*960";

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if($videoInfos['width'] >= 720 && $videoInfos['height'] >= 640) {
                    $medium_size = true;
                    $output_medium = "720*640";
                }
                $output_low = "90*80";

                $this->__orientation = 'Horizontal';

                break;

            case 8/9:  // Demi Ecran Vertical

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
                    $max_size = true;
                    $output_high = "960*1080";

                    copy($videoPath, $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'] . '/' . $videoInfos['fileName'] . '.mp4');

                    /*if($videoInfos['mediaType'] != 'sync') {
                        copy($videoPath, $old_path . 'HIGH/' . $videoInfos['fileName'] . '.mp4');
                    }*/
                }

                if ($videoInfos['width'] >= 640 && $videoInfos['height'] >= 720) {
                    $medium_size = true;
                    $output_medium = "640*720";
                }
                $output_low = "80*90";

                $this->__orientation = 'Vertical';

                break;

        }

        if ($max_size) {

            $copyFolder = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['high'];

            /*if($videoInfos['height'] === 2160) // 4k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['4k'];*/

            /*else if($videoInfos['height'] === 4320) // 8k
                $copyFolder .= '/' . $this->__encodeOutputSizesFolders['8k'];*/

            if(!file_exists($copyFolder))
                mkdir($copyFolder, 0777, true);

            //exec('ffmpeg -y -i "' . $videoPath . '" -r 25 -s ' . $output_high . ' -vcodec libx264 -b:v 4M -minrate 4M -maxrate 4M -profile:v high -level:v 4.2 -acodec copy -y "' . $copyFolder . '/' . $videoInfos['fileName'] . '.mp4"', $output, $cmdResultStatus);

            //dd('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -loglevel error -x264-params keyint_min=1:keyint=3:scenecut=0:bframes=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -b:v 15000000 -maxrate 30000000 -bufsize 15000000 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $copyFolder . '/' . $videoInfos['fileName'] . '.mp4"');

            // -s 1920*1080 -vf yadif,format=yuv420p -x264-params keyint_min=1:keyint=3:scenecut=0:bframes=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -b:v 15000000 -maxrate 30000000 -bufsize 15000000 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart
            exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_high . ' -vf yadif,format=yuv420p -loglevel debug -x264-params keyint_min=1:keyint=3:scenecut=0:bframes=0 -vcodec libx264 -preset slower -profile:v high -level 4.2 -r 25 -b:v 15000000 -maxrate 30000000 -bufsize 15000000 -coder cabac -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart "' . $copyFolder . '/' . $videoInfos['fileName'] . '.mp4"', $output,$cmdResultStatus);

            if($cmdResultStatus === 1)
            {
                dump($output);
                throw new Exception("Erreur lors de l'encodage du média en high");
            }

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

            exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_medium . ' -vf yadif,format=yuv420p -loglevel debug -x264-params keyint_min=2:keyint=9:scenecut=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -b:v 4000000 -maxrate 8000000 -bufsize 4000000 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart  "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['fileName'] . '.mp4"', $output,$cmdResultStatus);

            if($cmdResultStatus !== 0)
            {
                dump($output);
                throw new Exception("Erreur lors de l'encodage du média en medium");
            }

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['medium'] . '/' . $videoInfos['fileName'] . '.mp4';

            // Création de la vignette si au moins le format "medium" existe
            exec('ffmpeg -y -i "' . $videoPath . '" -s ' . $output_low . ' -vf yadif,format=yuv420p -loglevel debug -x264-params keyint_min=2:keyint=9:scenecut=0 -vcodec libx264 -preset slower -profile:v baseline -level 3.2 -r 25 -b:v 1000000 -maxrate 2000000 -bufsize 1000000 -coder vlc -flags cgop -color_primaries bt709 -color_trc bt709 -colorspace bt709 -an -movflags +faststart  "' . $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['fileName'] . '.mp4"', $output,$cmdResultStatus);

            if($cmdResultStatus !== 0)
            {
                dump($output);
                throw new Exception("Erreur lors de l'encodage du média en low");
            }

            $this->__filesToRenameWithId[] = $this->__mediasEncodeOutputFolder . '/' . $this->__encodeOutputSizesFolders['low'] . '/' . $videoInfos['fileName'] . '.mp4';

        }

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

    private function getVideoFileCharacteristics(string $pathToVideo)
    {

        $infofile = $video = $audio = $format = [];

        $path_to_json = $this->__parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
        exec('ffprobe -v quiet -print_format json -show_format -show_streams "' . $pathToVideo . '" > "' . $path_to_json . '"', $output, $error);

        if (!$error) {
            $datafile = file_get_contents($path_to_json);
            $flux = json_decode($datafile, true);
        } else {
            throw new Exception(sprintf("Errors : %s", implode(", ", $output)));
        }

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

        return $infofile;

    }

}