<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ElementGraphicRepository")
 */
class ElementGraphic extends Media
{

    /**
     * @ORM\ManyToOne(targetEntity="Contexte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contexte;


    public function __construct()
    {
        parent::__construct();
    }

    public function getContexte(): ?Contexte
    {
        return $this->contexte;
    }

    public function setContexte(?Contexte $contexte): self
    {
        $this->contexte = $contexte;

        return $this;
    }

}
