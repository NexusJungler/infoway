<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ScreenRepository")
 */
class Screen
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $serialNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $macAdress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firmware;
    /**
     * @ORM\Column(type="boolean")
     */
    private $majFirmwareAllowed ;

    /**
     * Many Screens have one site. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="screens")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getMacAdress(): ?string
    {
        return $this->macAdress;
    }

    public function setMacAdress(string $macAdress): self
    {
        $this->macAdress = $macAdress;

        return $this;
    }

    public function getFirmware(): ?string
    {
        return $this->firmware;
    }

    public function setFirmware(string $firmware): self
    {
        $this->firmware = $firmware;

        return $this;
    }
}
