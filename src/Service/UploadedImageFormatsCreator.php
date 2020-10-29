<?php


namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadedImageFormatsCreator
{

    private string $__mediasSourceFolder;

    private string $__mediaFormatsOutputFolder;

    private array $__processesOutputSizesFolders = [ 'low' => 'low', 'medium' => 'medium', 'high' => 'high', 'HD' => 'HD', '4k' => 'UHD-4k', '8k' => 'UHD-8k' ];

    private ParameterBagInterface $__parameterBag;

    private string $__orientation = "";

    private array $__acceptedRatios= [ 16/9, 9/16, 9/8, 8/9 ];

    private array $__errors = [];
    
    private array $__uploadedImageInfos = [];

    private array $__filesToRenameWithId = [];

    private int $__base_height = 6;

    private array $__currentMediaOutputFormat = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->__mediasSourceFolder = $parameterBag->get('project_dir') . '/../upload/source';
        $this->__mediaFormatsOutputFolder = $parameterBag->get('project_dir') . '/../upload/medias';
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
            $this->__mediasSourceFolder .= '/' . $this->__processesOutputSizesFolders['HD'] . '/' . $this->__processesOutputSizesFolders['4k'];

        else if($height === 4320) // 8k
            $this->__mediasSourceFolder .= '/' . $this->__processesOutputSizesFolders['HD'] . '/' . $this->__processesOutputSizesFolders['8k'];
        

        $pathToImg = $this->__mediasSourceFolder . '/' . $mediaInfos['name'] . '.' . $mediaInfos['extension'];

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

                    $this->__currentMediaOutputFormat[] = 'high';

                    $output['high'][0] = 1920;
                    $output['high'][1] = 1080;

                    $folder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['HD'];

                    if($height === 2160) // 4k
                    {
                        $this->__currentMediaOutputFormat[] = '4k';
                        $folder .= '/' . $this->__processesOutputSizesFolders['4k'];
                    }

                    else if($height === 4320) // 8k
                    {
                        $this->__currentMediaOutputFormat[] = '8k';
                        $folder .= '/' . $this->__processesOutputSizesFolders['8k'];
                    }

                    if(!file_exists($folder))
                        mkdir($folder, 0777, true);

                    if($width > 1920 && $height >1080)
                    {
                        imagepng($source, $folder . '/' . $mediaInfos['name'] . '.png');
                        $this->__filesToRenameWithId[] = $folder . '/' . $mediaInfos['name'] . '.png';
                    }

                }

                if ($width >= 1280 && $height >= 720) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output['medium'][0] = 1280;
                    $output['medium'][1] = 720;
                }

                if ($width >= 160 && $height >= 90) {

                    $this->__currentMediaOutputFormat[] = 'low';

                    $output['low'][0] = 160;
                    $output['low'][1] = 90;
                }

                $this->__orientation = 'Horizontal';

                break;

            case 9 / 16:   // Plein Ecran Vertical

                if ($width >= 1080 && $height >= 1920) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $output['high'][0] = 1080;
                    $output['high'][1] = 1920;

                    $folder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['HD'];

                    if($height === 2160) // 4k
                    {
                        $this->__currentMediaOutputFormat[] = '4k';
                        $folder .= '/' . $this->__processesOutputSizesFolders['4k'];
                    }

                    else if($height === 4320) // 8k
                    {
                        $this->__currentMediaOutputFormat[] = '8k';
                        $folder .= '/' . $this->__processesOutputSizesFolders['8k'];
                    }

                    if(!file_exists($folder))
                        mkdir($folder, 0777, true);

                    if ($width > 1080 && $height >1920)
                    {
                        imagepng($source, $folder . '/' . $mediaInfos['name'] . '.png');
                        $this->__filesToRenameWithId[] = $folder . '/' . $mediaInfos['name'] . '.png';
                    }
                }

                if ($width >= 720 && $height >= 1280) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output['medium'][0] = 720;
                    $output['medium'][1] = 1280;
                }

                if ($width >= 90 && $height >= 160) {

                    $this->__currentMediaOutputFormat[] = 'low';

                    $output['low'][0] = 90;
                    $output['low'][1] = 160;
                }

                $this->__orientation = 'Vertical';

                break;

            case 9 / 8:  // Demi Ecran Horizontal

                if ($width >= 1080 && $height >= 960) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $output['high'][0] = 1080;
                    $output['high'][1] = 960;
                    if ($width > 1080 && $height > 960) {
                        imagepng($source, $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['HD'] . '/' . $mediaInfos['name'] . '.png');
                    }
                }

                if ($width >= 720 && $height >= 640) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output['medium'][0] = 720;
                    $output['medium'][1] = 640;
                }

                if ($width >= 90 && $height >= 80) {

                    $this->__currentMediaOutputFormat[] = 'low';

                    $output['low'][0] = 90;
                    $output['low'][1] = 80;
                }

                $this->__orientation = 'Horizontal';

                break;

            case 8 / 9:  // Demi Ecran Vertical

                if ($width >= 960 && $height >= 1080) {

                    $this->__currentMediaOutputFormat[] = 'high';

                    $output['high'][0] = 960;
                    $output['high'][1] = 1080;
                    if ($width > 960 && $height > 1080) {
                        imagepng($source, $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['HD'] . '/' . $mediaInfos['name'] . '.png');
                    }
                }

                if ($width >= 640 && $height >= 720) {

                    $this->__currentMediaOutputFormat[] = 'medium';

                    $output['medium'][0] =640;
                    $output['medium'][1] = 720;
                }

                if ($width >= 80 && $height >= 90) {

                    $this->__currentMediaOutputFormat[] = 'low';

                    $output['low'][0] = 80;
                    $output['low'][1] = 90;
                }

                $this->__orientation = 'Vertical';

                break;

            default:  // situation où le ratio n'est pas standardisé
                // case élément graphique
                if($mediaInfos['mediaType'] == 'elmt') {

                    $this->__currentMediaOutputFormat[] = 'low';

                    // Why encoding more graphic element ??
                    $low_width = round($width/($height/$this->__base_height));
                    $output_low = $low_width . '*' . $this->__base_height;
                    $output['low'][0] = $output_low;
                    $output['low'][1] = $this->__base_height;
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

        $mediasHandler = new MediaInfosHandler($this->__parameterBag);

        foreach ($this->__processesOutputSizesFolders as $size => $folder)
        {

            if(isset($output[$size]))
            {

                // On vérifie que l'image a une résolution suffisante pour être redimensionnée
                $thumb = imagecreatetruecolor($output[$size][0], $output[$size][1]);

                $transparent = imagecolorallocate($thumb, 255, 255, 255);
                imagefilledrectangle($thumb, 0, 0, $output[$size][0], $output[$size][1], $transparent);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $output[$size][0], $output[$size][1], $width, $height);

                $path = $this->__mediaFormatsOutputFolder . '/' .  $size . '/' . $mediaInfos['name'] . '.png';

                if(!file_exists($this->__mediaFormatsOutputFolder . '/' .  $size))
                    mkdir($this->__mediaFormatsOutputFolder . '/' .  $size, 0777, true);

                imagepng($thumb, $path, 9, PNG_ALL_FILTERS);

                $this->__filesToRenameWithId[] = $path;

                $mediasHandler->changeImageDpi($path, $path,72)
                              ->convertImageCMYKToRGB($path, $path);

            }

        }

        // // siles differents formats du media n'ont pas été crée retourner false
        if(!$this->allMediaOutputFormatsIsCreated($mediaInfos))
            return false;

        $this->__uploadedImageInfos = [
            'name' => $mediaInfos['name'],
            'mediaType' => $mediaInfos['mediaType'],
            'ratio' => $mediaInfos['ratio'],
            'containIncruste' => false,
            'extension' => $mediaInfos['extension'],
            'orientation' => $this->__orientation,
            'mimeType' => $mediaInfos['mimeType'],
            'isArchived' => false,
            'width' => $width,
            'height' => $height,
            'size' => ( round(filesize($pathToImg)/(1024*1024), 2) > 0.00) ? round(filesize($pathToImg)/(1024*1024), 2) . ' Mo' : round(filesize($pathToImg), 2) . ' o',
            'createdAt' => $mediaInfos['createdAt'],
            'diffusionStart' => $mediaInfos['diffusionStart'],
            'diffusionEnd' => $mediaInfos['diffusionEnd'],
            'fileType' => "image",
        ];

        // on écrit les infos du media dans un json
        // les infos seront recupérés plus tard après la caractérisation du media puis seront supprimé du fichier
        /*$mediaToInsertDatasHandler = new MediaToInsertDatasHandler($this->__parameterBag);
        // renvoie l'index ou se situe les infos du media dans le json
        return $mediaToInsertDatasHandler->saveMediaInfosInJson($this->__uploadedImageInfos );*/

        return true;

    }

    private function allMediaOutputFormatsIsCreated($imageInfos)
    {

        $HDFolder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['HD'] . '/' . ( (in_array('4k', $this->__currentMediaOutputFormat)) ? $this->__processesOutputSizesFolders['4k'] : ((in_array('8k', $this->__currentMediaOutputFormat)) ? $this->__processesOutputSizesFolders['8k'] : "") );
        $highFolder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['high'];
        $mediumForlder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['medium'];
        $lowFolder = $this->__mediaFormatsOutputFolder . '/' . $this->__processesOutputSizesFolders['low'];

        if( in_array('4k', $this->__currentMediaOutputFormat) && !file_exists($HDFolder . '/' . $imageInfos['name']. ".png") )
        {
            $this->__errors[] = "Incomplete processes (Missing HD format)";
            return false;
        }
        else if( in_array('8k', $this->__currentMediaOutputFormat) && !file_exists($HDFolder . '/' . $imageInfos['name']. ".png") )
        {
            $this->__errors[] = "Incomplete processes (Missing HD format)";
            return false;
        }
        else if( in_array('high', $this->__currentMediaOutputFormat) && !file_exists($highFolder . '/' . $imageInfos['name']. ".png") )
        {
            $this->__errors[] = "Incomplete processes (Missing high format)";
            return false;
        }
        else if( in_array('medium', $this->__currentMediaOutputFormat) && !file_exists($mediumForlder . '/' . $imageInfos['name']. ".png") )
        {
            $this->__errors[] = "Incomplete processes (Missing medium format)";
            return false;
        }
        else if( in_array('low', $this->__currentMediaOutputFormat) && !file_exists($lowFolder . '/' . $imageInfos['name']. ".png") )
        {
            $this->__errors[] = "Incomplete processes (Missing low format)";
            return false;
        }
        else
            return true;

    }

}