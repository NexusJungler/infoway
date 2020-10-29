<?php


namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\MediaDiffusableRepository")
 */
class MediaDiffusable extends Media
{

    public function __construct()
    {
        parent::__construct();
    }

}