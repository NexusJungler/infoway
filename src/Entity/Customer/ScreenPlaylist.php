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
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="Media")
     * @ORM\JoinTable(name="screen_playlists_medias",
     *      joinColumns={@ORM\JoinColumn(name="playlist_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORm\JoinColumn(name="media_id", referencedColumnName="id")}
     *      )
     */
    private $medias;

    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="TimeSlot")
     * @ORM\JoinColumn(name="time_slot_id", referencedColumnName="id")
     */
    private $time_slot;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
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
        return $this->time_slot;
    }

    public function setTimeSlot(?TimeSlot $time_slot): self
    {
        $this->time_slot = $time_slot;

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
}
