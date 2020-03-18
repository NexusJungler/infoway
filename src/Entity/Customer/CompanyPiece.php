<?php

namespace App\Entity\Customer;


use App\Entity\Customer\CompanyPieceType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CompanyPieceRepository")
 */
class CompanyPiece
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Blank
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Blank
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Blank
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $city;


    /**
     * @ORM\ManyToOne(targetEntity="CompanyPieceType", inversedBy="CompanyPiece")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $country;

    /**
     * @ORM\Column(type="string")
     */
    private $logoName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(?int $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getType(): ?CompanyPieceType
    {
        return $this->type;
    }

    public function setType(?CompanyPieceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function setCountry(int $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLogoName(): string
    {
        return $this->logoName;
    }


    public function setLogoName(string $logoName): self
    {
        $this->logoName = $logoName;

        return $this;
    }



}
