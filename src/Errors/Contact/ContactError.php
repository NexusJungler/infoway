<?php

namespace App\Errors\Contact;

use App\Errors\Error;

class ContactError extends Error {


    /**
     * ContactError constructor.
     * @param string|null $message
     */
    public function __construct(?string $message)
    {
        $this->name = 'contact' ;
        parent::__construct() ;
    }



}


