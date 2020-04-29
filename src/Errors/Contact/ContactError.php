<?php

namespace App\Errors;

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
        parent::__construct('firstName need to be a string') ;
        $this->column = 'firstName' ;
    }

} ;

class InvalidContactLastNameError extends ContactError {
    public function __construct()
    {
        parent::__construct('lastName need to be a string') ;
        $this->column = 'lastName' ;
    }

} ;

class InvalidEmailError extends ContactError {
    public function __construct()
    {
        parent::__construct("Veuillez entrer un email valide") ;
        $this->column = 'email' ;
    }

} ;

class InvalidPhoneError extends ContactError {
    public function __construct()
    {
        parent::__construct("Veuillez entrer un telephone valide") ;
        $this->column = 'phone' ;
    }

} ;

class InvalidPositionError extends ContactError {
    public function __construct()
    {
        parent::__construct("Veuillez entrer un poste valide") ;
        $this->column = 'position' ;
    }

} ;