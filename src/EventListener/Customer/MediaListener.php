<?php


namespace App\EventListener\Customer;

use App\Entity\Customer\Media;
use App\Entity\Customer\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class MediaListener
{

    public function __construct( ) {


    }
    public function buildMediaCriterions(Media $media)
    {
        $media->setCriterions( new ArrayCollection() );

        foreach( $media->getProducts() as $product ){
            foreach( $product->getCriterions() as $criterion ){
                $media->addCriterion( $criterion ) ;
            }
        }
    }

}