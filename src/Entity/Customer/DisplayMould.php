<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\DisplayMouldRepository")
 */
class DisplayMould
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
     * @ORM\Column(type="integer")
     */
    private $screensNumber;

    /**
     * Many User have Many Phonenumbers.
     * @ORM\ManyToMany(targetEntity="Playlist")
     * @ORM\JoinTable(name="moulds_playlists",
     *      joinColumns={@ORM\JoinColumn(name="mould_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="playlist_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $playlists;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ScreenDisplay", mappedBy="mould")
     */
    private $generatedScreenDisplays;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="DisplaySpace", inversedBy="moulds")
     * @ORM\JoinColumn(name="display_space_id", referencedColumnName="id")
     */
    private $displaySpace;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Criterion", inversedBy="displayMoulds")
     * @ORM\JoinTable(name="display_moulds_criterions")
     */
    private $criterions;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="displayMoulds")
     * @ORM\JoinTable(name="display_moulds_tags")
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
     * @ORM\JoinTable(name="display_moulds_timeslots",
     *      joinColumns={@ORM\JoinColumn(name="display_mould_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="time_slot_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $timeSlots ;


    private ?DisplayMould $model = null;

    public function __construct() {
        $this->criterions = new ArrayCollection() ;
        $this->tags = new ArrayCollection() ;
        $this->playlists = new ArrayCollection();
        $this->generatedScreenDisplays = new ArrayCollection();
        $this->timeSlots = new ArrayCollection() ;
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
     * @return Collection|Playlist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
        }

        return $this;
    }

    /**
     * @return Collection|ScreenDisplay[]
     */
    public function getGeneratedScreenDisplays(): Collection
    {
        return $this->generatedScreenDisplays;
    }

    public function addGeneratedScreenDisplay(ScreenDisplay $generatedScreenDisplay): self
    {
        if (!$this->generatedScreenDisplays->contains($generatedScreenDisplay)) {
            $this->generatedScreenDisplays[] = $generatedScreenDisplay;
            $generatedScreenDisplay->setMould($this);
        }

        return $this;
    }

    public function removeGeneratedScreenDisplay(ScreenDisplay $generatedScreenDisplay): self
    {
        if ($this->generatedScreenDisplays->contains($generatedScreenDisplay)) {
            $this->generatedScreenDisplays->removeElement($generatedScreenDisplay);
            // set the owning side to null (unless already changed)
            if ($generatedScreenDisplay->getMould() === $this) {
                $generatedScreenDisplay->setMould(null);
            }
        }

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
     * @return DisplayMould|null
     */
    public function getModel(): ?DisplayMould
    {
        return $this->model;
    }

    /**
     * @param DisplayMould|null $model
     */
    public function setModel(?DisplayMould $model): self
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


}
