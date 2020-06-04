<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CheckoutProductRepository")
 */
class CheckoutProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\CheckoutSystem", inversedBy="checkoutProducts")
     * @ORM\JoinColumn(name="checkoutsystem", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $system;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private $app_product;

    /**
     * @ORM\Column(type="integer")
     */
    private $system_product_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSystem(): ?CheckoutSystem
    {
        return $this->system;
    }

    public function setSystem(?CheckoutSystem $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function getAppProduct(): ?Product
    {
        return $this->app_product;
    }

    public function setAppProduct(Product $app_product): self
    {
        $this->app_product = $app_product;

        return $this;
    }

    public function getSystemProductId(): ?int
    {
        return $this->system_product_id;
    }

    public function setSystemProductId(int $system_product_id): self
    {
        $this->system_product_id = $system_product_id;

        return $this;
    }
}
