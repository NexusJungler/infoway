<?php


namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaToInsertDatasHandler
{


    private ParameterBagInterface $__parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->__parameterBag = $parameterBag;
    }

    public function saveMediaInfosInJson(array $mediaInfos)
    {

        $pathToMediaToInsertInfos = $this->__parameterBag->get('project_dir') . '/..\upload\media_to_insert.json';
        if(!file_exists($pathToMediaToInsertInfos))
            fopen($pathToMediaToInsertInfos, "w");

        $fileContent = json_decode(file_get_contents($pathToMediaToInsertInfos,true));

        if(!is_array($fileContent))
        {
            $id = 0;
            $fileContent = [ $mediaInfos ];
        }
        else
        {

            $id = sizeof($fileContent);
            $fileContent[] = $mediaInfos;

        }

        file_put_contents($pathToMediaToInsertInfos, json_encode($fileContent));

        return $id;

    }

}