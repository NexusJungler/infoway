<?php

namespace App\Errors;

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
        if(!$this->column !== null && $this->message !== null ) $this->errorValue  = [$this->column => $this->message ] ;
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
        parent::__construct('Le nom choisi est invalide') ;
        $this->column = 'name' ;
    }

} ;

class InvalidCustomerSitesError extends CustomerError {
    public function __construct()
    {
        parent::__construct('Impossible de reconnaitre les sites selectionnés') ;
        $this->column = 'sites' ;
    }

} ;

class InvalidCustomerLogo  extends ContactError {
    public function __construct()
    {
        parent::__construct("Veuillez entrer un logo valide") ;
        $this->column = 'logo' ;
    }

} ;

class InvalidCustomerUsers extends ContactError {
    public function __construct()
    {
        parent::__construct("Impossible de reconnaitre les users selectionnés") ;
        $this->column = 'users' ;
    }

} ;

class InvalidCustomerContact extends ContactError {
    public function __construct()
    {
        parent::__construct("Veuillez entrer un contact valide") ;
        $this->column = 'contact' ;
    }

} ;