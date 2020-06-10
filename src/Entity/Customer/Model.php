<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ModelRepository")
 */
class Model
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $indice;

    /**
     * @ORM\Column(type="text")
     */
    private $css;

    /**
     * @ORM\Column(type="string", length=30,name="ligneUC")
     */
    private $ligneUC;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndice(): ?string
    {
        return $this->indice;
    }

    public function setIndice(string $indice): self
    {
        $this->indice = $indice;

        return $this;
    }

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss(string $css): self
    {
        $this->css = $css;

        return $this;
    }

    public function getLigneUC(): ?string
    {
        return $this->ligneUC;
    }

    public function setLigneUC(string $ligneUC): self
    {
        $this->ligneUC = $ligneUC;

        return $this;
    }
}
