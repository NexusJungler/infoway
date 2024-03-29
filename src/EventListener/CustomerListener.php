<?php

namespace App\EventListener;

use App\Entity\Admin\Customer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;


class CustomerListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function initializeSites(Customer $customer, $e)
    {

       $customer->setSites(new ArrayCollection());
    }
}