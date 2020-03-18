<?php


namespace App\Service;


use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class MediaUploaderService
{

    private $targetDirectory;

    public function __construct(string $targetDirectory = "uploads/")
    {
        $this->targetDirectory = $targetDirectory;
    }


    public function upload(UploadedFile $file)
    {

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try
        {

            $file->move($this->getTargetDirectory(), $fileName);

        }
        catch (FileException $e)
        {
            dump($e->getMessage()); die();
        }

        return $fileName;

    }


    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function setTargetDirectory(string $targetDirectory): self
    {
        $this->targetDirectory = $targetDirectory;

        return $this;
    }

}