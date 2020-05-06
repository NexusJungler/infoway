<?php

namespace App\Errors\Customer;
class InvalidCustomerNameError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Le nom choisi est invalide') ;
    }

} ;
