<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class MediaUploaderService
{

    private string $__targetDirectory;

    public function __construct(string $targetDirectory = "uploads/")
    {
        $this->__targetDirectory = $targetDirectory;
    }


    public function upload(UploadedFile $file)
    {

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try
        {

            $file->move($this->__targetDirectory, $fileName);

        }
        catch (FileException $e)
        {
            dump($e->getMessage()); die();
        }

        return $fileName;

    }


    public function getTargetDirectory()
    {
        return $this->__targetDirectory;
    }

    public function setTargetDirectory(string $targetDirectory): self
    {
        $this->__targetDirectory = $targetDirectory;

        return $this;
    }

}