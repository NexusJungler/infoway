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
     * @ORM\OneToMany(targetEntity="ScreenPlaylistEntry", mappedBy="playlist", cascade={"persist"})
     */
    private $entries;


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
            $entry->setPlaylist($this);
        }

        return $this;
    }

    public function removeEntry(ScreenPlaylistEntry $entry): self
    {
        if ($this->entries->contains($entry)) {
            $this->entries->removeElement($entry);
            // set the owning side to null (unless already changed)
            if ($entry->getPlaylist() === $this) {
                $entry->setPlaylist(null);
            }
        }

        return $this;
    }


}
