<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="Criterion", inversedBy="products")
     * @ORM\JoinTable(name="products_criterions")
     */
    private $criterions;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="products")
     * @ORM\JoinTable(name="products_tags")
     */
    private $tags;

    public function __construct() {
        $this->criterions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $criterion->addUser($this);
        }

        return $this;
    }

    public function removeCriterion(Criterion $criterion): self
    {
        if ($this->criterions->contains($criterion)) {
            $this->criterions->removeElement($criterion);
            $criterion->removeUser($this);
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
}
