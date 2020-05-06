<?php

namespace App\Errors\TagsList;

class NoTagToInsertError extends TagsListError {
public function __construct()
{
$this->column = 'tags' ;
parent::__construct("Vous n'avez saisi aucun tag") ;
}

} ;

