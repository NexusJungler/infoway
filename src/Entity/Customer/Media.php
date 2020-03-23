<?php

namespace App\Entity\Customer;

use App\Entity\Customer\Image;
use App\Entity\Customer\Video;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;



/**

 */
/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\MediaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="media_type", type="string")
 * @ORM\DiscriminatorMap({"media" = "Media","image" = "Image", "video" = "Video"})
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploaded_at;

    /**
     * NOTE:
     *      This is not a mapped field of entity metadata, just a simple property that will not stored in db
     *
     *      'mapping' is referencing to data in vich config file (config/packages/vich_uploader.yaml)
     *
     *      'fileNameProperty' is referencing to this entity property that will contain the name of the uploaded file
     *
     *       'size' is referencing to this entity property that will contain the size of the uploaded file
     *
     *       'mimeType' is referencing to this entity property that will contain the type of the uploaded file
     *
     * @Vich\UploadableField(mapping="mediaFile", fileNameProperty="fileName", size="size", mimeType="type")
     *
     * @var File
     */
    private $mediaFile;

    /**
     * @ORM\OneToOne(targetEntity="Video", mappedBy="media", cascade={"persist", "remove"})
     */
    private $video;

    /**
     * @ORM\OneToOne(targetEntity="Image", mappedBy="media", cascade={"persist", "remove"})
     */
    private $image;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }


    public function getMediaFile(): File
    {
        return $this->mediaFile;
    }


    public function setMediaFile(File $mediaFile): self
    {
        $this->mediaFile = $mediaFile;

        if($this->mediaFile instanceof UploadedFile)
        {

            $this->uploaded_at = new \DateTime('now');
            $this->setSize($mediaFile->getSize());

            $type = explode('/', $mediaFile->getMimeType())[0];
            $this->setType($type);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(\DateTimeInterface $uploaded_at): self
    {
        $this->uploaded_at = $uploaded_at;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(Video $video): self
    {
        $this->video = $video;

        // set the owning side of the relation if necessary
        if ($video->getMedia() !== $this) {
            $video->setMedia($this);
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        // set the owning side of the relation if necessary
        if ($image->getMedia() !== $this) {
            $image->setMedia($this);
        }

        return $this;
    }

}
