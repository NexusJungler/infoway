<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoElementGraphicRepository")
 * @ORM\Table(name="video_element_graphic")
 */
class VideoElementGraphic extends Video
{

    use ElementGraphic;

    public function __construct()
    {
        parent::__construct();
    }
}
