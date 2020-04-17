<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\SynchroRepository")
 */
class Synchro
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
    private $directory;

    /**
     * @ORM\Column(type="integer", name="nbr_files")
     */
    private $nbrFiles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $orientation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $format;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getNbrFiles(): ?int
    {
        return $this->nbrFiles;
    }

    public function setNbrFiles(int $nbrFiles): self
    {
        $this->nbrFiles = $nbrFiles;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}
