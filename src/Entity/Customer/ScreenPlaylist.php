<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ScreenPlaylistRepository")
 * @ORM\Table(name="screen_playlists")
 */
class ScreenPlaylist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $screenPosition;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ScreenPlaylistEntry", mappedBy="screenPlaylist")
     */
    private $entries;

    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="TimeSlot")
     * @ORM\JoinColumn(name="timeSlot_id", referencedColumnName="id")
     */
    private $timeSlot;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScreenPosition(): ?int
    {
        return $this->screenPosition;
    }

    public function setScreenPosition(int $screenPosition): self
    {
        $this->screenPosition = $screenPosition;

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

    /**
     * @return Collection|Media[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    /**
     * @return Collection|ScreenPlaylistEntry[]
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(ScreenPlaylistEntry $entry): self
    {
        if (!$this->entries->contains($entry)) {
            $this->entries[] = $entry;
            $entry->setScreenPlaylist($this);
        }

        return $this;
    }

    public function removeEntry(ScreenPlaylistEntry $entry): self
    {
        if ($this->entries->contains($entry)) {
            $this->entries->removeElement($entry);
            // set the owning side to null (unless already changed)
            if ($entry->getScreenPlaylist() === $this) {
                $entry->setScreenPlaylist(null);
            }
        }

        return $this;
    }
}
