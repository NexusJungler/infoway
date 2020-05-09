<?php
namespace App\Errors\Customer;

class CustomerCreationGeneralError extends CustomerError {
public function __construct()
{
$this->column = 'name' ;
parent::__construct("Une erreur est survenu lors de la tentative de création du client. Veuillez reessayer ") ;

}
}