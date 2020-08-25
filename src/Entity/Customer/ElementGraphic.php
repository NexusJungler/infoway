<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


trait ElementGraphic
{

    /**
     * @ORM\ManyToOne(targetEntity="Contexte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contexte;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="elementGraphics")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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
