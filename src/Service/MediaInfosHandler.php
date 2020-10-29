<?php


namespace App\Service;


use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaInfosHandler
{

    private $__parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->__parameterBag = $parameterBag;
    }

    public function getVideoFileCharacteristics(string $pathToVideo)
    {

        $infofile = $video = $audio = $format = [];

        $path_to_json = $this->__parameterBag->get('project_dir') . '/..\upload\infofile.json';

        if(!file_exists($path_to_json))
            fopen($path_to_json, "w");

        if(!is_writable($path_to_json))
            throw new Exception(sprintf("'%s' is not writable !", $path_to_json));

        exec('ffprobe -v quiet -print_format json -show_format -show_streams "' . $pathToVideo . '" > "' . $path_to_json . '"', $cmdOutput, $cmdResultStatus);

        if ($cmdResultStatus !== 0) {
            throw new Exception(sprintf("Errors : %s", implode(", ", $cmdOutput)));
        } else {
            $datafile = file_get_contents($path_to_json);
            $flux = json_decode($datafile, true);
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

    /**
     * Return video height, width and codec
     *
     * @param string $file
     * @return array
     */
    public function getVideoDimensions(string $file) {
        //$path_to_json = $this->parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
        $path_to_json = $this->__parameterBag->get('project_dir') . '/..\upload\infofile.json';

        if(!file_exists($path_to_json))
            fopen($path_to_json, "w");

        if(!is_writable($path_to_json))
            throw new Exception(sprintf("'%s' is not writable !", $path_to_json));

        $cmd = 'ffprobe -v quiet -print_format json -show_format -show_streams "'. $file . '" > "' . $path_to_json . '"';

        exec($cmd, $cmdOutput, $cmdResultStatus);

        if($cmdResultStatus !== 0)
        {
            dump($cmdOutput);die();
            throw new Exception(sprintf("Error during executing cmd : '%s'", $cmd));
        }

        $data = file_get_contents($path_to_json);
        $json = (array) json_decode($data);

        return [
            $json['streams'][0]->width,
            $json['streams'][0]->height,
            $json['streams'][0]->codec_long_name
        ];
    }

    /**
     * Return image width and height
     *
     * @param string $file
     * @return array
     * @throws Exception
     */
    public function getImageDimensions(string $file)
    {
        if(!file_exists($file))
            throw new Exception(sprintf("Cannot found this file : '%s'", $file));

        elseif(!is_readable($file))
            throw new Exception(sprintf("Cannot read this file : '%s'", $file));

        else
        {
            list($width, $height) = getimagesize($file);
            return [
              $width,
              $height,
            ];
        }
    }

    /**
     * Return image dpi
     *
     * @param string $filename
     * @return false|float|int|mixed|string
     */
    public function getImageDpi(string $filename){

        $cmd = "magick identify -quiet -format %x \"".$filename."\"  2>&1";

        exec($cmd, $cmdOutput, $cmdResultStatus);

        if($cmdResultStatus !== 0)
        {
            dump($cmdOutput);die();
            throw new Exception(sprintf("Error during executing cmd : '%s'", $cmd));
        }

        if($cmdOutput && is_array($cmdOutput)){
            $data = explode(' ', $cmdOutput[0]);

            if(array_key_exists(1, $data) && $data[1] == 'PixelsPerInch'){
                return intval($data[0]);
            }elseif(array_key_exists(1, $data) && $data[1] == 'PixelsPerCentimeter'){
                $x = ceil($data[0] * 2.54);
                return intval($x);
            }elseif(!array_key_exists(1, $data)){
                return intval($data[0]);
            }
        }
        return 72;
    }

    /**
     * Change image dpi
     *
     * @param string $originalFileName
     * @param string $outputFileName
     * @param int $dpi
     * @return self
     * @throws Exception
     */
    public function changeImageDpi(string $originalFileName, string $outputFileName, int $dpi)
    {

        if($this->getImageDpi($outputFileName) !== 72)
        {
            $cmd = "magick convert \"" . $originalFileName . "\" -density " . $dpi . " \"" . $outputFileName . "\"  2>&1";
            exec($cmd, $cmdOutput, $cmdResultStatus);

            if($cmdResultStatus === 1)
            {
                dump($cmdOutput);die();
                throw new Exception(sprintf("Error during executing cmd : '%s'", $cmd));
            }

        }

        return $this;
    }


    /**
     * Convert CMYK (Cyan Magenta Yellow Key(black) ) image to RGB (Red Green Black) image
     *
     * @param string $originalFileName
     * @param string $outputFileName
     * @return self
     * @throws Exception
     */
    public function convertImageCMYKToRGB(string $originalFileName, string $outputFileName)
    {

        $cmd = "magick convert \"" . $originalFileName . "\" -colorspace rgb \"" . $outputFileName . "\"  2>&1";
        exec($cmd, $cmdOutput, $cmdResultStatus);

        if($cmdResultStatus !== 0)
        {
            dump($cmdOutput);die();
            throw new Exception(sprintf("Error during executing cmd : '%s'", $cmd));
        }

        return $this;

    }


    /**
     * check with magick (@see: https://imagemagick.org/) if file is corrupted
     *
     * @param string $fileName
     * @return bool true if $cmdResultStatus === 1, else return false
     * @throws Exception
     */
    public function fileIsCorrupt(string $fileName, string $fileType)
    {

        if($fileType === 'image')
            $cmd = "magick identify -verbose \"" . $fileName . "\" 2>&1";

        else
            $cmd = "ffmpeg -v error -i \"" . $fileName . "\" -f null - 2>error.log";

        exec($cmd, $cmdOutput, $cmdResultStatus);

        if($cmdResultStatus !== 0)
        {
            dump($cmdOutput);
            throw new Exception(sprintf("Error during executing cmd : '%s'", $cmd));
        }

        if($fileType === 'video')
        {

            // if something is in error.log that file is considered as corrupt by ffmpeg
            if(filesize( $this->__parameterBag->get('project_dir') . "/public/error.log" ) !== 0)
                $output = 1;

            else
                $output = $cmdResultStatus;

            // remove log file created by ffmpeg
            unlink($this->__parameterBag->get('project_dir') . "/public/error.log");

        }
        else
            $output = $cmdResultStatus;

        return $output !== 0;
    }


}