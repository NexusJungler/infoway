<?php

namespace App\Entity\Customer;

use App\Entity\Customer\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ImageRepository")
 */
class Image extends Media
{
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ratio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $width;



    public function getRatio(): ?string
    {
        return $this->ratio;
    }

    public function setRatio(string $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }
}
