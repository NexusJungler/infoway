<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CriterionsListRepository")
 * @ORM\Table(name="criterions_lists")
 */
class CriterionsList
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

    private $basicCriterionUsed = false ;
    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="Criterion")
     * @ORM\JoinColumn(name="basic_criterion_id", referencedColumnName="id", nullable=true )
     */
    private $basicCriterion;


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
            if( $criterion === $this->getBasicCriterion() ) $this->setBasicCriterion( null ) ;
            // set the owning side to null (unless already changed)
            if ($criterion->getList() === $this) {
                $criterion->setList(null);
            }
        }

        return $this;
    }

    public function isBasicCriterionUsed() : bool {
        return $this->basicCriterionUsed ;
    }

    public function setBasicCriterionUsed(bool $basicCriterionUsed) : self {

        $this->basicCriterionUsed = $basicCriterionUsed ;

        return $this ;

    }
    public function getBasicCriterion(): ?Criterion
    {
        return $this->basicCriterion;
    }

    public function setBasicCriterion(?Criterion $basicCriterion): self
    {

        if( $basicCriterion !== null ){
            $this->addCriterion( $basicCriterion );
        }else{
            if( $this->basicCriterion !== null ){
                $this->removeCriterion( $this->basicCriterion );
            }
        }
        $this->basicCriterion = $basicCriterion;
        return $this;
    }


}
