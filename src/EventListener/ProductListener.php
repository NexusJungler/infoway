<?php

namespace App\EventListener;

use App\Entity\Admin\Customer;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;

class ProductListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function initializeAllergens(Product $product, $e)
    {
        $product->setAllergens(new ArrayCollection());
    }
}