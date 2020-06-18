<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\BroadcastSlotRepository")
 */
class BroadcastSlot
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Display", inversedBy="broadcastSlots")
     * @ORM\JoinColumn(name="display_id", referencedColumnName="id")
     */
    private $display;

    /**
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="ScreenPlaylist", cascade={"persist"})
     * @ORM\JoinTable(name="displays_screen_playlists",
     *      joinColumns={@ORM\JoinColumn(name="display_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="playlist_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $playlists;

    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="TimeSlot", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="timslot_id", referencedColumnName="id")
     */
    private $timeSlot;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
    }

    public function __clone()
    {
        $this->id = null ;
        $this->timeSlot = clone $this->timeSlot ;
        $this->playlists = $this->playlists->map( fn( ScreenPlaylist $screenPlaylist ) => clone $screenPlaylist ) ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|ScreenPlaylist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(ScreenPlaylist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
        }

        return $this;
    }

    public function removePlaylist(ScreenPlaylist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
        }

        return $this;
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getDisplay(): ?Display
    {
        return $this->display;
    }

    public function setDisplay(?Display $display): self
    {
        $this->display = $display;

        return $this;
    }

}
