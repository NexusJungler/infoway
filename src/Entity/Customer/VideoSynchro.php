<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoSynchroRepository")
 * @ORM\Table(name="video_synchro")
 */
class VideoSynchro extends Video
{

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    public function __construct()
    {
        parent::__construct();
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition($position): self
    {
        $this->position = $position;

        return $this;
    }



}
