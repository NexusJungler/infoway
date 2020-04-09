<?php


namespace App\Service;

use App\Entity\Admin\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

class UserHandler
{

    private SessionInterface $__session;

    public function __construct()
    {
      //  $this->__session = $session;
    }

    public function getUserFromSession() : ?User {
        $sessionManager = new SessionManager( new Session() ) ;

        $userFromSession = $sessionManager->get('user') ;
        //Si l object qu on recupere n est pas de type user on envoit une erreur
        if( ! $userFromSession instanceof User ) {
            throw new \Error('Impossible to get User from Session');
        }
        return  $userFromSession;
    }

}