<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ImageRepository")
 */
class Image extends Media
{

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $containIncruste;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Customer\Incruste", inversedBy="videos")
     */
    private $incrustes;

    public function __construct()
    {
        parent::__construct();
        $this->incrustes = new ArrayCollection();
    }

    public function getContainIncruste(): bool
    {
        return $this->containIncruste;
    }

    public function setContainIncruste(bool $containIncruste): self
    {
        $this->containIncruste = $containIncruste;

        return $this;
    }

    /**
     * @return Collection|Incruste[]
     */
    public function getIncrustes(): Collection
    {
        return $this->incrustes;
    }

    public function addIncruste(Incruste $incruste): self
    {
        if (!$this->incrustes->contains($incruste)) {
            $this->incrustes[] = $incruste;
        }

        return $this;
    }

    public function removeIncruste(Incruste $incruste): self
    {
        if ($this->incrustes->contains($incruste)) {
            $this->incrustes->removeElement($incruste);
        }

        return $this;
    }

}
