<?php

namespace App\Entity\Admin;

use App\Entity\Customer\VideoThematic;
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
     * @ORM\JoinColumn( name="video_thematics")
     */
    private $videoThematics;

    public function __construct()
    {
        $this->videoThematics = new ArrayCollection();
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


    public function setVideoThematics(ArrayCollection $videoThematics): self
    {
        $this->videoThematics = $videoThematics;

        return $this;
    }

    /**
     * @return Collection|VideoThematicThematicTheme[]
     */
    public function getVideoThematics(): Collection
    {
        return $this->videoThematics;
    }

    public function addVideoThematic(VideoThematic $videoThematic): self
    {
        if (!$this->videoThematics->contains($videoThematic)) {
            $this->videoThematics[] = $videoThematic;

            $videoThematic->setTheme($this->getId());
        }

        return $this;
    }

    public function removeVideoThematic(VideoThematic $videoThematic): self
    {
        if ($this->videoThematics->contains($videoThematic)) {
            $this->videoThematics->removeElement($videoThematic);
            // set the owning side to null (unless already changed)
            if ($videoThematic->getTheme() === $this->getId()) {
                // on utilise la valeur "0" pour que l'utilisatuer puisse attribuer une thematique ultérieurement
                // @TODO: la video devient inutilisable sans theme attribué !
                $videoThematic->setTheme(0);
            }
        }

        return $this;
    }
}
