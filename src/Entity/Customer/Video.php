<?php

namespace App\Entity\Customer;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoRepository")
 */
class Video extends Media
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $encodage;


    public function getEncodage(): ?string
    {
        return $this->encodage;
    }

    public function setEncodage(string $encodage): self
    {
        $this->encodage = $encodage;

        return $this;
    }

    
}
