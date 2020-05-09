<?php

namespace App\Errors\Contact;
class InvalidContactLastNameError extends ContactError {
    public function __construct()
    {
        $this->column = 'lastName' ;
        parent::__construct('lastName need to be a string') ;
    }

} ;
