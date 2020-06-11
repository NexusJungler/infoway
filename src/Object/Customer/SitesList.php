<?php

namespace App\Object\Customer ;

use App\Entity\Customer\Site ;
use \Doctrine\Common\Collections\ArrayCollection ;
class SitesList{

    private ArrayCollection $sites ;

    public function __construct(){
        $this->sites = new ArrayCollection() ;
    }

    public function addSite( Site $site ) : self{
        if( !$this->sites->contains( $site ) ){
            $this->sites[] = $site;
        }
        return $this;
    }

    public function removeSite( Site $site ) : self {
        if( $this->sites->contains( $site ) ){
            $this->sites->removeElement( $site ) ;
        }
        return $this;
    }

    public function getSites() : ArrayCollection{
        return $this->sites ;
    }

}