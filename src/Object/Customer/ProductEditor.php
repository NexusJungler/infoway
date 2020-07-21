<?php


namespace App\Object\Customer;

use App\Entity\Customer\CheckoutProduct;
use App\Entity\Customer\MainPrice;
use App\Entity\Customer\Product;
use Doctrine\Common\Collections\ArrayCollection;

class ProductEditor
{
    private Product $_product;
    private ArrayCollection $_prices;
    private ArrayCollection $_checkoutMappings;

    public function __construct()
    {
        $this->_prices = new ArrayCollection();
        $this->_checkoutMappings = new ArrayCollection();
    }

    public function setProduct(Product $product): self
    {
        $this->_product = $product;
        return $this;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function getPrices(): ArrayCollection
    {
        return $this->_prices;
    }

    public function getCheckoutMappings(): ArrayCollection
    {
        return $this->_checkoutMappings;
    }

    public function addPrice(MainPrice $price): self {
        if (!$this->_prices->contains($price)) {
            $this->_prices[] = $price;
        }
        return $this;
    }

    public function addMapping(CheckoutProduct $mapping): self {
        if (!$this->_checkoutMappings->contains($mapping)) {
            $this->_checkoutMappings[] = $mapping;
        }
        return $this;
    }
}
