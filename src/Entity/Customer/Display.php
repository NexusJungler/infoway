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
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="ScreenPlaylist")
     * @ORM\JoinTable(name="displays_screen_playlists",
     *      joinColumns={@ORM\JoinColumn(name="display_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="playlist_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $playlists;

    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="TimeSlot")
     * @ORM\JoinColumn(name="timslot_id", referencedColumnName="id")
     */
    private $timeSlot;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
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

}
