<?php

namespace App\Errors\TagsList;

class NotAllowedSitesError extends TagListsError {
public function __construct()
{
$this->column = 'sites' ;
parent::__construct('Vous avez saisi un ou plusieurs sites non valides ! ') ;
}

} ;
