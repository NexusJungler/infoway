<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CriterionListRepository")
 */
class CriterionList
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
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $multiple;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Criterion", mappedBy="list", cascade={"persist"})
     */
    private $criterions;
    // ...

    public function __construct() {
        $this->criterions = new ArrayCollection();
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

    public function getMultiple(): ?bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Criterion[]
     */
    public function getCriterions(): Collection
    {
        return $this->criterions;
    }

    public function addCriterion(Criterion $criterion): self
    {
        if (!$this->criterions->contains($criterion)) {
            $this->criterions[] = $criterion;
            $criterion->setList($this);
        }

        return $this;
    }

    public function removeCriterion(Criterion $criterion): self
    {
        if ($this->criterions->contains($criterion)) {
            $this->criterions->removeElement($criterion);
            // set the owning side to null (unless already changed)
            if ($criterion->getList() === $this) {
                $criterion->setList(null);
            }
        }

        return $this;
    }
}
