<?php

namespace App\Errors\CriterionsList;

use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Error;

class CriterionsListError extends Error {

    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        $this->name = 'criterions_list' ;
        parent::__construct( $message ) ;
    }

}


