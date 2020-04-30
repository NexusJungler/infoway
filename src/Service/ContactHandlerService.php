<?php
namespace App\Service;



use App\Entity\Admin\Contact;
use App\Errors\ContactError;
use App\Errors\CustomerError;
use App\Errors\InvalidContactFirstNameError;
use App\Errors\InvalidContactLastNameError;
use App\Errors\InvalidEmailError;
use App\Errors\InvalidPhoneError;
use App\Errors\InvalidPositionError;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ContactHandlerService {
    public function __construct(FlashBagHandler $flashbagHandler)
    {
        $this->flashbagHandler = $flashbagHandler;
    }


    public function isFirstNameValid( $firstName , ?CustomerError $errorParent = null) : bool{
        if(  is_string($firstName) ){
            return true;
        }else{
            $error = new InvalidContactFirstNameError() ;
            if($errorParent !== null ){
                $errorParent->addChildError($error);
                $error = $errorParent ;
            }
            $this->flashbagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isLastNameInvalid($lastName, ?CustomerError $errorParent = null) : bool{
        if(  is_string($lastName) ){
            return true;
        }else{
            $error = new InvalidContactLastNameError() ;
            if($errorParent !== null ){
                $errorParent->addChildError($error);
                $error = $errorParent ;
            }
            $this->flashbagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isEmailValid($email, ?CustomerError $errorParent = null) : bool{
        if(  is_string($email) ){
            return true;
        }else{
            $error = new InvalidEmailError() ;
            if($errorParent !== null ){
                $errorParent->addChildError($error);
                $error = $errorParent ;
            }
            $this->flashbagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isPhoneValid($telephone, ?CustomerError $errorParent = null) : bool{
        if(  is_string($telephone) ){
            return true;
        }else{
            $error = new InvalidPhoneError() ;
            if($errorParent !== null ){
                $errorParent->addChildError($error);
                $error = $errorParent ;
            }
            $this->flashbagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function isPositionValid($position, ?CustomerError $errorParent = null) : bool{
        if(  is_string($position) ){
            return true;
        }else{
            $error = new InvalidPositionError() ;
            if($errorParent !== null ){
                $errorParent->addChildError($error);
                $error = $errorParent ;
            }
            $this->flashbagHandler->getFlashBag()->set('error', $error->errorToArray() ) ;
            return false;
        }
    }

    public function areContactDatasValids( Contact $contactDatas ,?CustomerError $errorParent = null){
      return(
           $this->isFirstNameValid( $contactDatas->getFirstName(), $errorParent ) &&
           $this->isLastNameInvalid( $contactDatas->getLastName(), $errorParent ) &&
           $this->isEmailValid( $contactDatas->getMail(), $errorParent ) &&
           $this->isPhoneValid( $contactDatas->getTelephone(), $errorParent ) &&
           $this->isPositionValid( $contactDatas->getPosition(),$errorParent )
      ) ;
    }

    public function isNameIsValid(int $name) : bool{
        return is_string($name) ;
    }
}