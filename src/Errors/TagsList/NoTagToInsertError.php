<?php

namespace App\Errors\TagsList;

class NoTagToInsertError extends TagListsError {
public function __construct()
{
$this->column = 'tags' ;
parent::__construct("Vous n'avez saisi aucun tag") ;
}

} ;

