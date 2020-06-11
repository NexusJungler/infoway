<?php

namespace App\Errors\Criterion;
class NotExistingProductsError extends CriterionError {
    public function __construct()
    {
        $this->column = 'products' ;
        parent::__construct('Un ou plusieurs produits saisis sont incorrectes') ;
    }

} ;
