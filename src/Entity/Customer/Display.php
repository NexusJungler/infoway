<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\DisplayRepository")
 */
class Display
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="BroadcastSlot", mappedBy="display")
     */
    private $broadcastSlots;


    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->broadcastSlots = new ArrayCollection();
    }

    public function __clone()
    {
       $this->id = null ;
       $this->broadcastSlots = $this->broadcastSlots->map( fn( BroadcastSlot $broadcastSlot ) => clone $broadcastSlot ) ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|BroadcastSlot[]
     */
    public function getBroadcastSlots(): Collection
    {
        return $this->broadcastSlots;
    }

    public function addBroadcastSlot(BroadcastSlot $broadcastSlot): self
    {
        if (!$this->broadcastSlots->contains($broadcastSlot)) {
            $this->broadcastSlots[] = $broadcastSlot;
            $broadcastSlot->setDisplay($this);
        }

        return $this;
    }

    public function removeBroadcastSlot(BroadcastSlot $broadcastSlot): self
    {
        if ($this->broadcastSlots->contains($broadcastSlot)) {
            $this->broadcastSlots->removeElement($broadcastSlot);
            // set the owning side to null (unless already changed)
            if ($broadcastSlot->getDisplay() === $this) {
                $broadcastSlot->setDisplay(null);
            }
        }

        return $this;
    }



}
