<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ImageElementGraphicRepository")
 * @ORM\Table(name="image_element_graphic")
 */
class ImageElementGraphic extends ElementGraphic
{

    public function __construct()
    {
        parent::__construct();
    }

}
