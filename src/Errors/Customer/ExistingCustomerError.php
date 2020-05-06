<?php

namespace App\Errors\Customer;
class ExistingCustomerError extends CustomerError {
public function __construct()
{
$this->column = 'name' ;
parent::__construct("Un client portant le même nom existe déja ") ;
}
}