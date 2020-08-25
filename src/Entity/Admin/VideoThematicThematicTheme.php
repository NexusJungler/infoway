<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\VideoThematicThematicThemeRepository")
 * @ORM\Table(name="videothematic_thematictheme")
 */
class VideoThematicThematicTheme
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="video_thematic_id")
     */
    private $videoThematicId;

    /**
     * @ORM\ManyToOne(targetEntity="ThematicTheme", inversedBy="videoThematic")
     * @ORM\JoinColumn(nullable=false, name="thematic_theme")
     */
    private $thematicTheme;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideoThematicId(): ?int
    {
        return $this->videoThematicId;
    }

    public function setVideoThematicId(int $videoThematicId): self
    {
        $this->videoThematicId = $videoThematicId;

        return $this;
    }

    public function getThematicTheme(): ThematicTheme
    {
        return $this->thematicTheme;
    }

    public function setThematicTheme(ThematicTheme $thematicTheme): self
    {
        $this->thematicTheme = $thematicTheme;

        return $this;
    }

}
