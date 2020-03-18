<?php

namespace App\Entity\Customer;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CompanyPieceTypeRepository")
 */
class CompanyPieceType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPiece", mappedBy="type", orphanRemoval=true)
     */
    private $companyPieces;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    public function __construct()
    {
        $this->companyPieces = new ArrayCollection();
    }

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

    /**
     * @return Collection|CompanyPiece[]
     */
    public function getCompanyPieces(): Collection
    {
        return $this->companyPieces;
    }

    public function addCompanyPiece(CompanyPiece $companyPiece): self
    {
        if (!$this->companyPieces->contains($companyPiece)) {
            $this->companyPieces[] = $companyPiece;
            $companyPiece->setType($this);
        }

        return $this;
    }

    public function removeCompanyPiece(CompanyPiece $companyPiece): self
    {
        if ($this->companyPieces->contains($companyPiece)) {
            $this->companyPieces->removeElement($companyPiece);
            // set the owning side to null (unless already changed)
            if ($companyPiece->getType() === $this) {
                $companyPiece->setType(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
