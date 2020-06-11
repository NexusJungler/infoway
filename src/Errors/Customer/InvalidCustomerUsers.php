<?php

namespace App\Errors\Customer;
class InvalidCustomerUsers extends CustomerError {
    public function __construct()
    {
        $this->column = 'users' ;
        parent::__construct("Impossible de reconnaitre les users selectionnés") ;
    }

} ;
