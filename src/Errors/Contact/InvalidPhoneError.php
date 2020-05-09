<?php

namespace App\Errors\Contact;
class InvalidPhoneError extends ContactError {
    public function __construct()
    {
        $this->column = 'phone' ;
        parent::__construct("Veuillez entrer un telephone valide") ;
    }

} ;
