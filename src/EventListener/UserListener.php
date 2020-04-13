<?php

namespace App\EventListener;

use App\Entity\Admin\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;


class UserListener
{

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    private $_doctrine ;
    /**
     * UserListener constructor.
     */
    public function __construct($doctrine)
    {
       $this->_doctrine = $doctrine ;
    }

    public function initializeSites(User $user, $e)
    {
       $user->setSites(new ArrayCollection());
    }
}