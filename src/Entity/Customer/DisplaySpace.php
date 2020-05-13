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
     * @ORM\OneToMany(targetEntity="DisplayMould", mappedBy="displaySpace")
     */
    private $moulds;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ScreenDisplay", mappedBy="displaySpace")
     */
    private $screenDisplays;

    public function __construct() {
        $this->moulds = new ArrayCollection();
        $this->screenDisplays = new ArrayCollection() ;
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
     * @return Collection|DisplayMould[]
     */
    public function getMoulds(): Collection
    {
        return $this->moulds;
    }

    public function addMould(DisplayMould $mould): self
    {
        if (!$this->moulds->contains($mould)) {
            $this->moulds[] = $mould;
            $mould->setDisplaySpace($this);
        }

        return $this;
    }

    public function removeMould(DisplayMould $mould): self
    {
        if ($this->moulds->contains($mould)) {
            $this->moulds->removeElement($mould);
            // set the owning side to null (unless already changed)
            if ($mould->getDisplaySpace() === $this) {
                $mould->setDisplaySpace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ScreenDisplay[]
     */
    public function getScreenDisplays(): Collection
    {
        return $this->screenDisplays;
    }

    public function addScreenDisplay(ScreenDisplay $screenDisplay): self
    {
        if (!$this->screenDisplays->contains($screenDisplay)) {
            $this->screenDisplays[] = $screenDisplay;
            $screenDisplay->setDisplaySpace($this);
        }

        return $this;
    }

    public function removeScreenDisplay(ScreenDisplay $screenDisplay): self
    {
        if ($this->screenDisplays->contains($screenDisplay)) {
            $this->screenDisplays->removeElement($screenDisplay);
            // set the owning side to null (unless already changed)
            if ($screenDisplay->getDisplaySpace() === $this) {
                $screenDisplay->setDisplaySpace(null);
            }
        }

        return $this;
    }
}
