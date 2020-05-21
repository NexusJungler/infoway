<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\LocalProgrammingRepository")
 * @ORM\Table(name="local_programmings")
 */
class LocalProgramming
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
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="Display")
     * @ORM\JoinTable(name="local_programming_displays",
     *      joinColumns={@ORM\JoinColumn(name="local_programming_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="display_id", referencedColumnName="id", unique=true)}
     *      )
     *
     */
    private $displays;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ProgrammingMould", inversedBy="generatedLocalProgrammings")
     * @ORM\JoinColumn(name="mould_id", referencedColumnName="id")
     */
    private $mould;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="DisplaySpace", inversedBy="LocalProgrammings")
     * @ORM\JoinColumn(name="display_space_id", referencedColumnName="id")
     */
    private $displaySpace;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Criterion", inversedBy="LocalProgrammings")
     * @ORM\JoinTable(name="local_programmings_criterions")
     */
    private $criterions;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="LocalProgrammings")
     * @ORM\JoinTable(name="local_programmings_tags")
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="TimeSlot")
     * @ORM\JoinTable(name="local_programmings_timeslots",
     *      joinColumns={@ORM\JoinColumn(name="local_programming_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="time_slot_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $timeSlots ;


    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->criterions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->timeSlots = new ArrayCollection() ;
        $this->displays = new ArrayCollection();
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


    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }



    public function getMould(): ?ProgrammingMould
    {
        return $this->mould;
    }

    public function generateLocalProgrammingFromMould(ProgrammingMould $mould){

        $this->setName( $mould->getName() ) ;

        foreach( $mould->getDisplays() as $display ){
            $this->addDisplay( $display );
        }

        foreach( $mould->getTags() as $tag ){
            $this->addTag( $tag );
        }

        foreach( $mould->getCriterions() as $criterion ){
            $this->addCriterion( $criterion );
        }

        foreach( $mould->getTimeSlots() as $timeSlot ){
            $this->addTimeSlot( $timeSlot );
        }

        $this->setStartAt( $mould->getStartAt() );

        $this->setEndAt( $mould->getEndAt() ) ;

        $mould->addGeneratedLocalProgramming( $this ) ;

        return $this;
    }

    public function setMould(?ProgrammingMould $mould): self
    {
        $this->mould = $mould;
        $this->generateLocalProgrammingFromMould( $mould );
        $mould->addGeneratedLocalProgramming($this);

        return $this;
    }

    public function getDisplaySpace(): ?DisplaySpace
    {
        return $this->displaySpace;
    }

    public function setDisplaySpace(?DisplaySpace $displaySpace): self
    {
        $this->displaySpace = $displaySpace;

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
        }

        return $this;
    }

    public function removeCriterion(Criterion $criterion): self
    {
        if ($this->criterions->contains($criterion)) {
            $this->criterions->removeElement($criterion);
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|TimeSlot[]
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function addTimeSlot(TimeSlot $timeSlot): self
    {
        if (!$this->timeSlots->contains($timeSlot)) {
            $this->timeSlots[] = $timeSlot;
        }

        return $this;
    }

    public function removeTimeSlot(TimeSlot $timeSlot): self
    {
        if ($this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->removeElement($timeSlot);
        }

        return $this;
    }

    /**
     * @return Collection|Display[]
     */
    public function getDisplays(): Collection
    {
        return $this->displays;
    }

    public function addDisplay(Display $display): self
    {
        if (!$this->displays->contains($display)) {
            $this->displays[] = $display;
        }

        return $this;
    }

    public function removeDisplay(Display $display): self
    {
        if ($this->displays->contains($display)) {
            $this->displays->removeElement($display);
        }

        return $this;
    }
}
