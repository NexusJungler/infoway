<?php


namespace App\Service;


use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediasHandler
{

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Return video height, width and codec
     *
     * @param string $file
     * @return array
     */
    public function getVideoDimensions(string $file) {
        //$path_to_json = $this->parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
        $path_to_json = $this->parameterBag->get('project_dir') . '/..\upload\infofile.json';

        if(!file_exists($path_to_json))
            throw new Exception(sprintf("Cannot found this file : '%s'", $path_to_json));

        $cmd = 'ffprobe -v quiet -print_format json -show_format -show_streams "'. $file . '" > "' . $path_to_json . '"';

        exec($cmd, $cmdOutput, $cmdResultStatus);

        if($cmdResultStatus !== 0)
        {
            dump($cmdOutput);
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
              'width' => $width,
              'height' => $height,
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
            dump($cmdOutput);
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
                dump($cmdOutput);
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
            dump($cmdOutput);
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
            if(filesize( $this->parameterBag->get('project_dir') . "/public/error.log" ) !== 0)
                $output = 1;

            else
                $output = $cmdResultStatus;

            // remove log file created by ffmpeg
            unlink($this->parameterBag->get('project_dir') . "/public/error.log");

        }
        else
            $output = $cmdResultStatus;

        return $output !== 0;
    }


}