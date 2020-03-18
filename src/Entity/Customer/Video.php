<?php

namespace App\Entity\Customer;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Media", inversedBy="video", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $encodage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $extension;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getEncodage(): ?string
    {
        return $this->encodage;
    }

    public function setEncodage(string $encodage): self
    {
        $this->encodage = $encodage;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
    
}
