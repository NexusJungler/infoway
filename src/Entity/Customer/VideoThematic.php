<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoThematicRepository")
 * @ORM\Table(name="video_thematic")
 */
class VideoThematic extends Video
{

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTheme(): ?int
    {
        return $this->theme;
    }

    public function setTheme(?int $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

}
