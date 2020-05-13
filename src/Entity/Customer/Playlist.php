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
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="features")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
}
