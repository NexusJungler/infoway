<?php
namespace App\Service;


use App\Errors\Error;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashBagHandler {

    private $session ;
    private $flashbag ;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->flashbag = $this->session->getFlashBag() ;
    }

    public function getFlashBag() : FlashBagInterface {
        return $this->flashbag ;
    }

    public function addErrorInFlashbag(Error $error) : void {
        $this->getFlashBag()->set('error', $error->errorToArray() ) ;
    }


}