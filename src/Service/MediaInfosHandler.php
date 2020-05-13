<?php


namespace App\Service;


use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaInfosHandler
{

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getVideoDimensions($file) {
        //$path_to_json = 'C:\inetpub\wwwroot\tmp\infofile.json';
        $path_to_json = $this->parameterBag->get('project_dir') . '/..\inetpub\wwwroot\tmp\infofile.json';
        exec('ffprobe -v quiet -print_format json -show_format -show_streams "'. $file . '" > "' . $path_to_json . '"');
        $data = file_get_contents($path_to_json);
        $json = (array) json_decode($data);

        return [
            $json['streams'][0]->width,
            $json['streams'][0]->height,
            $json['streams'][0]->codec_long_name
        ];
    }

    public function getImageDimensions($file)
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

    public function getImageDpi($file)
    {
        $a = fopen($file,'r');
        $string = fread($a,20);
        fclose($a);

        $data = bin2hex(substr($string,14,4));
        $x = substr($data,0,4);
        $y = substr($data,0,4);

        return [
            hexdec($x),
            hexdec($y)
        ];

    }

    function getImageDpi2($filename){
        $cmd = 'identify -quiet -format "%x" '.$filename;
        @exec(escapeshellcmd($cmd), $data);
        if($data && is_array($data)){
            $data = explode(' ', $data[0]);

            if($data[1] == 'PixelsPerInch'){
                return $data[0];
            }elseif($data[1] == 'PixelsPerCentimeter'){
                $x = ceil($data[0] * 2.54);
                return $x;
            }elseif($data[1] == 'Undefined'){
                return $data[0];
            }
        }
        return 73;
    }

}