<?php

namespace App\Errors\CriterionsList;
class ExistingCriterionsListNameError extends CriterionsListError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Une liste de critères porte déjà ce nom. Veuillez en choisir un autre') ;
    }

} ;

