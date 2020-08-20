<?php

namespace App\Entity\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\ThematicThemeRepository")
 */
class ThematicTheme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="VideoThematicThematicTheme", mappedBy="thematicTheme", orphanRemoval=true)
     * @ORM\JoinColumn( name="video_thematic")
     */
    private $videoThematic;

    public function __construct()
    {
        $this->videoThematic = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|VideoThematicThematicTheme[]
     */
    public function getVideoThematic(): Collection
    {
        return $this->videoThematic;
    }

    public function addVideoThematic(VideoThematicThematicTheme $videoThematic): self
    {
        if (!$this->videoThematic->contains($videoThematic)) {
            $this->videoThematic[] = $videoThematic;
            $videoThematic->setThematicTheme($this);
        }

        return $this;
    }

    public function removeVideoThematic(VideoThematicThematicTheme $videoThematic): self
    {
        if ($this->videoThematic->contains($videoThematic)) {
            $this->videoThematic->removeElement($videoThematic);
            // set the owning side to null (unless already changed)
            if ($videoThematic->getThematicTheme() === $this) {
                $videoThematic->setThematicTheme(null);
            }
        }

        return $this;
    }
}
