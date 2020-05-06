<?php

namespace App\Errors\Customer;
class InvalidCustomerSitesError extends CustomerError {
    public function __construct()
    {
        $this->column = 'sites' ;
        parent::__construct('Impossible de reconnaitre les sites selectionnés') ;
    }

} ;
