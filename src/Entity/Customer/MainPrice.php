<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\MainPriceRepository")
 */
class MainPrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\PricesFactory", inversedBy="product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $factory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Product")
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $day_value;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $night_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFactory(): ?PricesFactory
    {
        return $this->factory;
    }

    public function setFactory(?PricesFactory $factory): self
    {
        $this->factory = $factory;

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

    public function getDayValue(): ?string
    {
        return $this->day_value;
    }

    public function setDayValue(string $day_value): self
    {
        $this->day_value = $day_value;

        return $this;
    }

    public function getNightValue(): ?string
    {
        return $this->night_value;
    }

    public function setNightValue(string $night_value): self
    {
        $this->night_value = $night_value;

        return $this;
    }

    /*

     * @ORM\Column(type="boolean")
    private $turnedOn;

    public function getTurnedOn(): ?bool
    {
        return $this->turnedOn;
    }

    public function setTurnedOn(bool $turnedOn): self
    {
        $this->turnedOn = $turnedOn;

        return $this;
    }
    */
}
