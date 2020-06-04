<?php


namespace App\EventListener\Customer;

use App\Entity\Customer\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class ProductListener
{

    public function __construct( ) {
       
        
    }
    public function initializeAllergens(Product $product, $e)
    {
        $product->setAllergens(new ArrayCollection());
    }

}