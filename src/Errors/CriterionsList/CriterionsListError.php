<?php

namespace App\Errors\Customer;

use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Error;

class CriterionsListError extends Error {

    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct( $message ) ;
    }

}


class ExistingCriterionsListNameError extends CriterionsListError {
    public function __construct()
    {
        $this->column = 'name' ;
        parent::__construct('Une liste de critères porte déjà ce nom. Veuillez en choisir un autre') ;
    }

} ;

class MinimumCriterionsInListNonReachedError extends CriterionsListError {
    public function __construct( int $minimumCriterionsInList)
    {
        $this->column = 'general' ;
        parent::__construct("Veuillez choisir au moins $minimumCriterionsInList critères à ajouter à la liste") ;
    }
}

