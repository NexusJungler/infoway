<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\DisplaySpaceRepository")
 */
class DisplaySpace
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
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="LocalProgramming", mappedBy="displaySpace")
     */
    private $localProgrammings;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="DisplaySetting", mappedBy="displaySpace", cascade={"persist"})
     */
    private $displaySettings;

    public function __construct() {
        $this->localProgrammings = new ArrayCollection() ;
        $this->displaySettings = new ArrayCollection() ;
    }

    public function setId( int $id ): self{
        $this->id = $id ;

        return $this;
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
     * @return Collection|localProgramming[]
     */
    public function getlocalProgrammings(): Collection
    {
        return $this->localProgrammings;
    }

    public function addlocalProgramming(localProgramming $localProgramming): self
    {
        if (!$this->localProgrammings->contains($localProgramming)) {
            $this->localProgrammings[] = $localProgramming;
            $localProgramming->setDisplaySpace($this);
        }

        return $this;
    }

    public function removelocalProgramming(localProgramming $localProgramming): self
    {
        if ($this->localProgrammings->contains($localProgramming)) {
            $this->localProgrammings->removeElement($localProgramming);
            // set the owning side to null (unless already changed)
            if ($localProgramming->getDisplaySpace() === $this) {
                $localProgramming->setDisplaySpace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DisplaySetting[]
     */
    public function getDisplaySettings(): Collection
    {
        return $this->displaySettings;
    }

    public function addDisplaySetting(DisplaySetting $displaySetting): self
    {
        if (!$this->displaySettings->contains($displaySetting)) {
            $this->displaySettings[] = $displaySetting;
            $displaySetting->setDisplaySpace($this);
        }

        return $this;
    }

    public function removeDisplaySetting(DisplaySetting $displaySetting): self
    {
        if ($this->displaySettings->contains($displaySetting)) {
            $this->displaySettings->removeElement($displaySetting);
            // set the owning side to null (unless already changed)
            if ($displaySetting->getDisplaySpace() === $this) {
                $displaySetting->setDisplaySpace(null);
            }
        }

        return $this;
    }
}
