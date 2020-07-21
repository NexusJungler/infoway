<?php

namespace App\Errors\Customer;
class InvalidCustomerLogo  extends CustomerError {
    public function __construct()
    {
        $this->column = 'logo' ;
        parent::__construct("Veuillez entrer un logo valide") ;
    }

} ;
