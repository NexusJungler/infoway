<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoThematicRepository")
 * @ORM\Table(name="video_thematic")
 */
class VideoThematic extends Video
{

    use Thematic;

    public function __construct()
    {
        parent::__construct();
    }

}
