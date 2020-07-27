<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\SynchroElementRepository")
 */
class SynchroElement extends Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity="Synchro", mappedBy="videos")
     */
    private $synchros;

    public function __construct()
    {
        parent::__construct();
        $this->synchros = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|Synchro[]
     */
    public function getSynchros(): Collection
    {
        return $this->synchros;
    }

    public function addSynchro(Synchro $synchro): self
    {
        if (!$this->synchros->contains($synchro)) {
            $this->synchros[] = $synchro;
            $synchro->addVideo($this);
        }

        return $this;
    }

    public function removeSynchro(Synchro $synchro): self
    {
        if ($this->synchros->contains($synchro)) {
            $this->synchros->removeElement($synchro);
            $synchro->removeVideo($this);
        }

        return $this;
    }
}
