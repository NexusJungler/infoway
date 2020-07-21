<?php

namespace App\Errors\Contact;
class InvalidContactFirstNameError extends ContactError {
    public function __construct()
    {
        $this->column = 'firstName' ;
        parent::__construct('firstName need to be a string') ;
    }

} ;