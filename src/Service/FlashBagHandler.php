<?php
namespace App\Service;


use App\Errors\Error;
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashBagHandler {

    private $flashbag ;

    public function __construct(FlashBagInterface $flashbag)
    {

        $this->flashbag = $flashbag;

    }

    public function getFlashBagContainer() : FlashBagInterface{
        return $this->flashbag ;
    }


    public function getFlashBag( string $name ){
        return $this->flashbag->get( $name ) ;
    }
    public function getOneFlashBagOrNul( string $name ) {
        $flashbagIntercepted = $this->flashbag->get($name) ;
        return  count( $flashbagIntercepted )  === 1  ?   $flashbagIntercepted[0] :  null  ;
    }

    public function addErrorInFlashbag(Error $error) : void {
        $this->flashbag->set('error', $error->errorToArray() ) ;
    }

    public function addFlashBag(string $name, string $data){
        $this->flashbag->set( $name, $data) ;
    }


}