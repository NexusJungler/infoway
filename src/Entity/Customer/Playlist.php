<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\PlaylistRepository")
 */
class Playlist
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
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="TimeSlot")
     * @ORM\JoinColumn(name="time_slot_id", referencedColumnName="id")
     */
    private $time_slot;

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
}
