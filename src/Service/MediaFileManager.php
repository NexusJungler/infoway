<?php


namespace App\Service;


use App\Entity\Customer\Media;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaFileManager
{

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $__parameterBag;

    /**
     * @var SessionManager
     */
    private SessionManager $__sessionManager;

    /**
     * @var Media
     */
    private Media $__currentMedia;

    private string $__currentMediaCustomerName;


    public function __construct(ParameterBagInterface $parameterBag, SessionManager $sessionManager, Media $media)
    {
        $this->__parameterBag = $parameterBag;
        $this->__sessionManager = $sessionManager;
        $this->__currentMedia = $media;
        $this->__currentMediaCustomerName = strtolower($this->__sessionManager->get('current_customer')->getName());
    }

    public function removeMediaFiles()
    {

        $fileType = explode("/", $this->__currentMedia->getMimeType())[0];
        $sizes = ['low', 'medium', 'high', 'HD', 'high/UHD-4k', 'high/UHD-8k', 'HD/UHD-4k', 'HD/UHD-8k', 'HIGH/UHD-4k', 'HIGH/UHD-8k'];
        $root = $this->__parameterBag->get('project_dir') .'/../upload/medias/' . $this->__currentMediaCustomerName . '/' . $fileType . '/' . $this->__currentMedia->getMediaType();
        foreach ($sizes as $size)
        {

            $path = $root . '/' .$size .'/' . $this->__currentMedia->getId() . ( ($fileType === 'image') ? '.png' : '.mp4' );

            if(file_exists($path))
                unlink($path);

        }

        return true;

    }

    public function duplicateMediaFiles(int $newId)
    {

        $fileType = explode("/", $this->__currentMedia->getMimeType())[0];
        $sizes = ['low', 'medium', 'high', 'HD', 'high/UHD-4k', 'high/UHD-8k', 'HD/UHD-4k', 'HD/UHD-8k', 'HIGH/UHD-4k', 'HIGH/UHD-8k'];
        $root = $this->__parameterBag->get('project_dir') .'/../upload/medias/' . $this->__currentMediaCustomerName . '/' . $fileType . '/' . $this->__currentMedia->getMediaType();
        foreach ($sizes as $size)
        {

            $path = $root . '/' .$size .'/' . $this->__currentMedia->getId() . ( ($fileType === 'image') ? '.png' : '.mp4' );

            if(file_exists($path))
                copy($path, str_replace($this->__currentMedia->getId(), $newId, $path));

        }

        return true;
    }

}