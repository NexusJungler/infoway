<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ProgrammingMouldRepository")
 * @ORM\Table(name="programming_moulds")
 */
class ProgrammingMould
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"mouldSerialization"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mouldSerialization"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"mouldSerialization"})
     */
    private $screensNumber;

    /**
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="Display", cascade={"persist"})
     * @ORM\JoinTable(name="displays_programming_moulds",
     *      joinColumns={@ORM\JoinColumn(name="mould_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="display_id", referencedColumnName="id", unique=true)}
     *      )
     *
     */
    private $displays;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="LocalProgramming", mappedBy="mould")
     */
    private $generatedLocalProgrammings;

    /**
     * @ORM\ManyToOne(targetEntity="DisplaySetting", cascade={"persist"})
     * @ORM\JoinColumn(name="setting_id", referencedColumnName="id")
     */
    private $displaySetting;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Criterion", inversedBy="ProgrammingMoulds" , cascade={"persist"} )
     * @ORM\JoinTable(name="programming_moulds_criterions")
     * @Groups({"mouldSerialization"})
     */
    private $criterions;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="ProgrammingMoulds", cascade={"persist"} )
     * @ORM\JoinTable(name="programming_moulds_tags")
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
     * @ORM\ManyToMany(targetEntity="TimeSlot", cascade={"persist"})
     * @ORM\JoinTable(name="programming_moulds_timeslots",
     *      joinColumns={@ORM\JoinColumn(name="programming_mould_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="time_slot_id", referencedColumnName="id", unique=true)}
     *      )
     * @Groups({"mouldSerialization"})
     */
    private $timeSlots ;


    private ?ProgrammingMould $model = null;

    public function __construct() {
        $this->criterions = new ArrayCollection() ;
        $this->tags = new ArrayCollection() ;
        $this->playlists = new ArrayCollection();
        $this->generatedLocalProgrammings = new ArrayCollection();
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

    public function getScreensNumber(): ?int
    {
        return $this->screensNumber;
    }

    public function setScreensNumber(int $screensNumber): self
    {
        $this->screensNumber = $screensNumber;

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


    /**
     * @return Collection|LocalProgramming[]
     */
    public function getGeneratedLocalProgrammings(): Collection
    {
        return $this->generatedLocalProgrammings;
    }

    public function addGeneratedLocalProgramming(LocalProgramming $generatedLocalProgramming): self
    {
        if (!$this->generatedLocalProgrammings->contains($generatedLocalProgramming)) {
            $this->generatedLocalProgrammings[] = $generatedLocalProgramming;
            $generatedLocalProgramming->setMould($this);
        }

        return $this;
    }

    public function removeGeneratedLocalProgramming(LocalProgramming $generatedLocalProgramming): self
    {
        if ($this->generatedLocalProgrammings->contains($generatedLocalProgramming)) {
            $this->generatedLocalProgrammings->removeElement($generatedLocalProgramming);
            // set the owning side to null (unless already changed)
            if ($generatedLocalProgramming->getMould() === $this) {
                $generatedLocalProgramming->setMould(null);
            }
        }

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
     * @return ProgrammingMould|null
     */
    public function getModel(): ?ProgrammingMould
    {
        return $this->model;
    }

    /**
     * @param ProgrammingMould|null $model
     */
    public function setModel(?ProgrammingMould $model): self
    {
        $this->model = $model;
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

    public function getDisplaySetting(): ?DisplaySetting
    {
        return $this->displaySetting;
    }

    public function setDisplaySetting(?DisplaySetting $displaySetting): self
    {
        $this->displaySetting = $displaySetting;

        return $this;
    }


}
