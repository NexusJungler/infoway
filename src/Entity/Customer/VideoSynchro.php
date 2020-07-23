<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoSynchroRepository")
 * @ORM\Table(name="video_synchro")
 */
class VideoSynchro extends Video
{
    public function __construct()
    {
        parent::__construct();
    }
}
