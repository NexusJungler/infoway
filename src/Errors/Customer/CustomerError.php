<?php

namespace App\Errors\Customer;

use App\Entity\Admin\Customer;
use App\Errors\Error;

class CustomerError extends Error{


    /**
     * ContactError constructor.
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        $this->name = 'customer' ;
        parent::__construct($message);
    }

}

