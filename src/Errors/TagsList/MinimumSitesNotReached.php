<?php
namespace App\Errors\TagsList;

class MinimumSitesNotReached extends TagsListError {

    public function __construct(int $minimumSitesInTags)
    {
    $this->column = 'sites' ;
    parent::__construct("Vous devez saisir au moins $minimumSitesInTags site(s) à ajouter ") ;
    }

} ;