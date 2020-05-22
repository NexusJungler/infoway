<?php

namespace App\Entity\Admin;

use App\Entity\Customer\Site;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\ScreenRepository")
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

    private $site ;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;


    public function __construct()
    {
        $this->available = true;
    }


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

    public function getMajFirmwareAllowed(): ?bool
    {
        return $this->majFirmwareAllowed;
    }

    public function setMajFirmwareAllowed(bool $majFirmwareAllowed): self
    {
        $this->majFirmwareAllowed = $majFirmwareAllowed;

        return $this;
    }


    public function getSite() : Site
    {
        return $this->site;
    }


    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this ;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }



}
