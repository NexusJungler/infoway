<?php
namespace App\Service;

class ArrayHandler{

    public function filterArrayById( $arrayToFilter ){
        $arrayFilteredById = [] ;
        foreach( $arrayToFilter as $entryToFilter ) {
            if( ! method_exists($entryToFilter, 'getId') )  throw new \Error('cannot find Getter') ;
                $arrayFilteredById[ $entryToFilter->getId() ] = $entryToFilter ;
        }
        return  $arrayFilteredById ;
    }
}