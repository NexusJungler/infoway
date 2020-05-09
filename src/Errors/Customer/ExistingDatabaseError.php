<?php

namespace App\Errors\Customer;
class ExistingDatabaseError extends CustomerError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct("Impossible de saisir ce nom, veuillez en choisir un autre") ;

    }
}

