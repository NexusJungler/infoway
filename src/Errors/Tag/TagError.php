<?php

namespace App\Errors\Tag;

use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Error;

class TagError extends Error
{

/**
* @param string|null $message
*/
public function __construct(?string $message = null)
{
    $this->name = 'tag' ;
parent::__construct($message);
}

}




