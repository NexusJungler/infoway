<?php

namespace App\Errors\Criterion;
class NotExistingSitesError extends CriterionError {
    public function __construct()
    {
        $this->column = 'sites' ;
        parent::__construct('Un ou plusieurs sites saisis sont incorrectes') ;
    }

} ;
