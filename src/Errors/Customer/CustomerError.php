<?php

namespace App\Errors;

use App\Entity\Admin\Customer;

class CustomerError {

    private ?string $message = null ;
    protected ?string $column = null ;
    protected array $errorValue = [] ;

    /**
     * ContactError constructor.
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        $this->message = $message;
        if($this->column !== null && $this->message !== null ) $this->errorValue  = [$this->column => $this->message ] ;

    }


    public function setMessage( string $message ){
        $this->message = $message ;
    }

    public function addChildError(ContactError $contactError){
        $this->errorValue = $contactError->errorToArray() ;
    }

    public function errorToArray(){
        return ['customer' => $this->errorValue ] ;
    }
}


class InvalidCustomerNameError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Le nom choisi est invalide') ;
    }

} ;

class InvalidCustomerSitesError extends CustomerError {
    public function __construct()
    {
        $this->column = 'sites' ;
        parent::__construct('Impossible de reconnaitre les sites selectionnés') ;
    }

} ;

class InvalidCustomerLogo  extends CustomerError {
    public function __construct()
    {
        $this->column = 'logo' ;
        parent::__construct("Veuillez entrer un logo valide") ;
    }

} ;

class InvalidCustomerUsers extends CustomerError {
    public function __construct()
    {
        $this->column = 'users' ;
        parent::__construct("Impossible de reconnaitre les users selectionnés") ;
    }

} ;

class InvalidCustomerContact extends CustomerError {
    public function __construct()
    {
        $this->column = 'contact' ;
        parent::__construct("Veuillez entrer un contact valide") ;
    }

} ;

class ExistingCustomerError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct("Un client portant le même nom existe déja ") ;
    }
}

class ExistingDatabaseError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct("Impossible de saisir ce nom, veuillez en choisir un autre") ;

    }
}

class CustomerCreationGeneralError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct("Une erreur est survenu lors de la tentative de création du client. Veuillez reessayer ") ;

    }
}
class CustomerDatabaseCreationError extends CustomerCreationGeneralError {
    public function __construct()
    {
        parent::__construct() ;

    }
}
