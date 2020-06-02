<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CheckoutSystemRepository")
 */
class CheckoutSystem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer\CheckoutProduct", mappedBy="system", orphanRemoval=true)
     */
    private $checkoutProducts;

    public function __construct()
    {
        $this->checkoutProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|CheckoutProduct[]
     */
    public function getCheckoutProducts(): Collection
    {
        return $this->checkoutProducts;
    }

    public function addCheckoutProduct(CheckoutProduct $checkoutProduct): self
    {
        if (!$this->checkoutProducts->contains($checkoutProduct)) {
            $this->checkoutProducts[] = $checkoutProduct;
            $checkoutProduct->setSystem($this);
        }

        return $this;
    }

    public function removeCheckoutProduct(CheckoutProduct $checkoutProduct): self
    {
        if ($this->checkoutProducts->contains($checkoutProduct)) {
            $this->checkoutProducts->removeElement($checkoutProduct);
            // set the owning side to null (unless already changed)
            if ($checkoutProduct->getSystem() === $this) {
                $checkoutProduct->setSystem(null);
            }
        }

        return $this;
    }
}
