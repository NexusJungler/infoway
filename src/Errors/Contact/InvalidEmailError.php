<?php

namespace App\Errors\Contact;
class InvalidEmailError extends ContactError {
    public function __construct()
    {
        $this->column = 'email' ;
        parent::__construct("Veuillez entrer un email valide") ;
    }

} ;
