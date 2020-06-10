<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ElementGraphicRepository")
 * @ORM\Table(name="element_graphic")
 */
class ElementGraphic extends Media
{

    /**
     * @ORM\ManyToOne(targetEntity="Contexte")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contexte;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="elementGraphics")
     */
    private $products;

    public function __construct()
    {
        parent::__construct();
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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addElementGraphic($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeElementGraphic($this);
        }

        return $this;
    }
}
