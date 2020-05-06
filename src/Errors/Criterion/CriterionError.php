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
        $this->name = 'criterion' ;
        parent::__construct($message);
    }

}
