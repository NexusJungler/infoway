<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ScreenPlaylistEntryRepository")
 * @ORM\Table(name="screen_playlist_entries")
 */
class ScreenPlaylistEntry
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
    private $positionInPlaylist;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ScreenPlaylist", inversedBy="entries")
     * @ORM\JoinColumn(name="playlist_id", referencedColumnName="id")
     */
    private $playlistId;

    /**
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    private $media;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPositionInPlaylist(): ?int
    {
        return $this->positionInPlaylist;
    }

    public function setPositionInPlaylist(int $positionInPlaylist): self
    {
        $this->positionInPlaylist = $positionInPlaylist;

        return $this;
    }

    public function getPlaylistId(): ?ScreenPlaylist
    {
        return $this->playlistId;
    }

    public function setPlaylistId(?ScreenPlaylist $playlistId): self
    {
        $this->playlistId = $playlistId;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}
