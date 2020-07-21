<?php

namespace App\Errors\Criterion;
class ExistingCriterionNameError extends CriterionError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Un critère existant porte déjà ce nom. Veuillez en choisir un autre') ;
    }

} ;
