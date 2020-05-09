<?php
namespace App\Errors\Contact;
class InvalidPositionError extends ContactError {
    public function __construct()
    {
        $this->column = 'position' ;
        parent::__construct("Veuillez entrer un poste valide") ;
    }

} ;