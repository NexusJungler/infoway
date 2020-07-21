<?php

namespace App\EventListener\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ManagerRegistry;


class SiteListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function initializeUsers(Site $site, $e)
    {
        $site->setUsers( new ArrayCollection() );
        $site->setScreens( new ArrayCollection() ) ;

    }

}