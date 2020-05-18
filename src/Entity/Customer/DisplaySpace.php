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
     * @ORM\OneToMany(targetEntity="ProgrammingMould", mappedBy="displaySpace")
     */
    private $moulds;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="LocalProgramming", mappedBy="displaySpace")
     */
    private $localProgrammings;

    public function __construct() {
        $this->moulds = new ArrayCollection();
        $this->localProgrammings = new ArrayCollection() ;
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
     * @return Collection|ProgrammingMould[]
     */
    public function getMoulds(): Collection
    {
        return $this->moulds;
    }

    public function addMould(ProgrammingMould $mould): self
    {
        if (!$this->moulds->contains($mould)) {
            $this->moulds[] = $mould;
            $mould->setDisplaySpace($this);
        }

        return $this;
    }

    public function removeMould(ProgrammingMould $mould): self
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
}
