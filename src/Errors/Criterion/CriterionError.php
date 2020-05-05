<?php

namespace App\Errors\Criterion;

use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Error;

class CriterionError extends Error
{

    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

}
class ExistingCriterionNameError extends CriterionError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Un critère existant porte déjà ce nom. Veuillez en choisir un autre') ;
    }

} ;

class NotExistingSitesError extends CriterionError {
    public function __construct()
    {
        $this->column = 'sites' ;
        parent::__construct('Un ou plusieurs sites saisis sont incorrectes') ;
    }

} ;

class NotExistingProductsError extends CriterionError {
    public function __construct()
    {
        $this->column = 'products' ;
        parent::__construct('Un ou plusieurs produits saisis sont incorrectes') ;
    }

} ;
