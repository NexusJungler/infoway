<?php


namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadedImageFormatsCreator
{

    private string $__mediasSourceFolder;

    private string $__mediaFormatsOutputFolder;

    private array $__encodeOutputSizesFolders = [ 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'HD' => 'HD', '4k' => 'UHD-4k', '8k' => 'UHD-8k' ];

    private ParameterBagInterface $__parameterBag;

    private string $__mediaOrientation = "";

    private array $__acceptedRatios= [ 16/9, 9/16, 9/8, 8/9 ];

    private array $__errors = [];
    
    private array $__uploadedImageInfos = [];

    private array $__filesToRenameWithId = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->__mediasSourceFolder = $parameterBag->get('project_dir') . '/../upload/source';
        $this->__mediaFormatsOutputFolder = $parameterBag->get('project_dir') . '/../upload/medias_encode';
        $this->__parameterBag = $parameterBag;
    }

    public function getErrors()
    {
        return $this->__errors;
    }

    public function getImageInfos()
    {
        return $this->__uploadedImageInfos;
    }

    public function getFilesToRenameList()
    {
        return $this->__filesToRenameWithId;
    }

    public function createImageFormats(array $mediaInfos)
    {

        $this->__mediasSourceFolder .= '/' . $mediaInfos['customerName'] . '/image/' . $mediaInfos['mediaType'];
        $this->__mediaFormatsOutputFolder .= '/' . $mediaInfos['customerName'] . '/image/' . $mediaInfos['mediaType'];

        $width = $mediaInfos['width'];
        $height = $mediaInfos['height'];

        if($height === 2160) // 4k
            $this->__mediasSourceFolder .= '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $this->__encodeOutputSizesFolders['4k'];

        /*else if($height === 4320) // 8k
            $this->__mediasSourceFolder .= '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $this->__encodeOutputSizesFolders['8k'];
        */

        $pathToImg = $this->__mediasSourceFolder . '/' . $mediaInfos['fileName'] . '.' . $mediaInfos['extension'];

        if(!file_exists($pathToImg))
            throw new Exception(sprintf("File not found : %s", $pathToImg));

        $this->__filesToRenameWithId[] = $pathToImg;

        //list($width, $height) = getimagesize($pathToImg);
        $output = [];

        $ratio = $width / $height;

        if($mediaInfos['extension'] == 'png')
            $source = imagecreatefrompng($pathToImg);

        else
            $source = imagecreatefromstring(file_get_contents($pathToImg));

        if(!in_array($ratio, $this->__acceptedRatios))
        {
            $this->__errors[] = "bad ratio($width/$height)";
            return false;
        }

        switch ($ratio) {
            case 16 / 9:  // Plein Ecran Horizontal

                if ($width >= 1920 && $height >= 1080) {
                    $output['high'][0] = 1920;
                    $output['high'][1] = 1080;

                    $folder = $this->__mediaFormatsOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'];

                    if($height === 2160) // 4k
                        $folder .= '/' . $this->__encodeOutputSizesFolders['4k'];

                    /*else if($height === 4320) // 8k
                        $folder .= '/' . $this->__encodeOutputSizesFolders['8k'];
                    */

                    if(!file_exists($folder))
                        mkdir($folder, 0777, true);

                    if($width > 1920 && $height >1080)
                        imagepng($source, $folder . '/' . $mediaInfos['fileName'] . '.png');

                }

                if ($width >= 1280 && $height >= 720) {
                    $output['medium'][0] = 1280;
                    $output['medium'][1] = 720;
                }

                if ($width >= 160 && $height >= 90) {
                    $output['low'][0] = 160;
                    $output['low'][1] = 90;
                }

                $this->__mediaOrientation = 'Horizontal';

                break;

            case 9 / 16:   // Plein Ecran Vertical

                if ($width >= 1080 && $height >= 1920) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 1920;

                    $folder = $this->__mediaFormatsOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'];

                    if($height === 2160) // 4k
                        $folder .= '/' . $this->__encodeOutputSizesFolders['4k'];

                    /*else if($height === 4320) // 8k
                        $folder .= '/' . $this->__encodeOutputSizesFolders['8k'];
                    */

                    if(!file_exists($folder))
                        mkdir($folder, 0777, true);

                    if ($width > 1080 && $height >1920)
                        imagepng($source, $folder . '/' . $mediaInfos['fileName'] . '.png');
                }

                if ($width >= 720 && $height >= 1280) {
                    $output['medium'][0] = 720;
                    $output['medium'][1] = 1280;
                }

                if ($width >= 90 && $height >= 160) {
                    $output['low'][0] = 90;
                    $output['low'][1] = 160;
                }

                $this->__mediaOrientation = 'Vertical';

                break;

            case 9 / 8:  // Demi Ecran Horizontal

                if ($width >= 1080 && $height >= 960) {
                    $output['high'][0] = 1080;
                    $output['high'][1] = 960;
                    if ($width > 1080 && $height > 960) {
                        imagepng($source, $this->__mediaFormatsOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $mediaInfos['fileName'] . '.png');
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

                $this->__mediaOrientation = 'Horizontal';

                break;

            case 8 / 9:  // Demi Ecran Vertical

                if ($width >= 960 && $height >= 1080) {
                    $output['high'][0] = 960;
                    $output['high'][1] = 1080;
                    if ($width > 960 && $height > 1080) {
                        imagepng($source, $this->__mediaFormatsOutputFolder . '/' . $this->__encodeOutputSizesFolders['HD'] . '/' . $mediaInfos['fileName'] . '.png');
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

                $this->__mediaOrientation = 'Vertical';

                break;

            default:  // situation où le ratio n'est pas standardisé
                // case élément graphique
                if($mediaInfos['mediaType'] == 'elmt') {
                    // Why encoding more graphic element ??
                    $low_width = round($width/($height/base_height));
                    $output_low = $low_width . '*' . base_height;
                    $output['low'][0] = $output_low;
                    $output['low'][1] = base_height;
                }
                else
                {
                    $this->__errors[] = "bad ratio($width / $height)";
                }
                // case média diffusable
                if($mediaInfos['mediaType'] == 'diff') {

                    return false; // On exclut l'insertion en base
                }
                break;
        }

        $mediasHandler = new MediasHandler($this->__parameterBag);

        foreach ($this->__encodeOutputSizesFolders as $size => $folder)
        {

            if(isset($output[$size]))
            {

                // On vérifie que l'image a une résolution suffisante pour être redimensionnée
                $thumb = imagecreatetruecolor($output[$size][0], $output[$size][1]);

                $transparent = imagecolorallocate($thumb, 255, 255, 255);
                imagefilledrectangle($thumb, 0, 0, $output[$size][0], $output[$size][1], $transparent);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $output[$size][0], $output[$size][1], $width, $height);

                $path = $this->__mediaFormatsOutputFolder . '/' .  $size . '/' . $mediaInfos['fileName'] . '.png';

                if(!file_exists($this->__mediaFormatsOutputFolder . '/' .  $size))
                    mkdir($this->__mediaFormatsOutputFolder . '/' .  $size, 0777, true);

                imagepng($thumb, $path, 9, PNG_ALL_FILTERS);

                $this->__filesToRenameWithId[] = $path;

                $mediasHandler->changeImageDpi($path, $path,72)
                              ->convertImageCMYKToRGB($path, $path);

            }

        }

        $this->__uploadedImageInfos = [
            'filename' => $mediaInfos['fileName'],
            'mediaType' => $mediaInfos['mediaType'],
            'ratio' => "$width/$height",
            'extension' => $mediaInfos['extension'],
            'orientation' => $this->__mediaOrientation,
            'mimeType' => $mediaInfos['mimeType'],
            'width' => $width,
            'height' => $height,
            'size' => ( round(filesize($pathToImg)/(1024*1024), 2) > 0.00) ? round(filesize($pathToImg)/(1024*1024), 2) . ' Mo' : round(filesize($pathToImg), 2) . ' o'
        ];

        return true;

    }

}