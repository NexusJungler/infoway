<?php

namespace App\Errors\CriterionsList;
class MinimumCriterionsInListNonReachedError extends CriterionsListError {
    public function __construct( int $minimumCriterionsInList)
    {
        $this->column = 'general' ;
        parent::__construct("Veuillez choisir au moins $minimumCriterionsInList critères à ajouter à la liste") ;
    }
}