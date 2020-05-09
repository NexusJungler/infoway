<?php

namespace App\Errors\Customer;
class InvalidCustomerContact extends CustomerError {
    public function __construct()
    {
        $this->column = 'contact' ;
        parent::__construct("Veuillez entrer un contact valide") ;
    }

} ;