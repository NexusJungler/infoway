<?php

namespace App\Errors\TagsList;

use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Error;

class TagListsError extends Error
{

    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        $this->name = 'tags_list' ;
        parent::__construct($message);
    }

}




