<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\PriceRepository")
 */
class Price
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="PricesGroup", inversedBy="prices")
     * @ORM\JoinColumn(name="prices_group_id", referencedColumnName="id")
     */
    private $pricesGroup;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPricesGroup(): ?PricesGroup
    {
        return $this->pricesGroup;
    }

    public function setPricesGroup(?PricesGroup $pricesGroup): self
    {
        $this->pricesGroup = $pricesGroup;

        return $this;
    }
}
