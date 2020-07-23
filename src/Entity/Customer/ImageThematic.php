<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ImageThematicRepository")
 * @ORM\Table(name="image_thematic")
 */
class ImageThematic extends Image
{

    use Thematic;

}
