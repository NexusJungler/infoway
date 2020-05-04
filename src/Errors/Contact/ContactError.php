<?php

namespace App\Errors\Contact;

class ContactError {

    private ?string $message = null ;
    protected ?string $column = null ;

    /**
     * ContactError constructor.
     * @param string|null $message
     */
    public function __construct(?string $message)
    {
        $this->message = $message;
    }


    public function setMessage( string $message ){
        $this->message = $message ;
    }

    public function errorToArray(){
        return ['contact' => [ $this->column => $this->message] ] ;
    }
}


class InvalidContactFirstNameError extends ContactError {
    public function __construct()
    {
        $this->column = 'firstName' ;
        parent::__construct('firstName need to be a string') ;
    }

} ;

class InvalidContactLastNameError extends ContactError {
    public function __construct()
    {
        $this->column = 'lastName' ;
        parent::__construct('lastName need to be a string') ;
    }

} ;

class InvalidEmailError extends ContactError {
    public function __construct()
    {
        $this->column = 'email' ;
        parent::__construct("Veuillez entrer un email valide") ;
    }

} ;

class InvalidPhoneError extends ContactError {
    public function __construct()
    {
        $this->column = 'phone' ;
        parent::__construct("Veuillez entrer un telephone valide") ;
    }

} ;

class InvalidPositionError extends ContactError {
    public function __construct()
    {
        $this->column = 'position' ;
        parent::__construct("Veuillez entrer un poste valide") ;
    }

} ;