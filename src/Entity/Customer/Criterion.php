<?php

namespace App\Entity\Customer;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\CriterionRepository")
 */
class Criterion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"mouldSerialization"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mouldSerialization"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;


    /**
     * @ORM\ManyToOne(targetEntity="CriterionsList", inversedBy="criterions", cascade={"persist"})
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id")
     */
    private $list;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="criterions",cascade={"persist"})
     * @Groups({"mouldSerialization"})
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity="Site", mappedBy="criterions", cascade={"persist"} )
     */
    private $sites;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="ProgrammingMould", mappedBy="criterions")
     * @Groups({"mouldSerialization"})
     */
    private $ProgrammingMoulds;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="LocalProgramming", mappedBy="criterions")
     */
    private $LocalProgrammings;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selected;


    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->position = 1 ;
        $this->selected = false ;
        $this->ProgrammingMoulds = new ArrayCollection() ;
        $this->LocalProgrammings = new ArrayCollection() ;
    }

    public function setId( int $id ): self{
        $this->id = $id ;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

//    /** @ORM\PostLoad */
//    public function doStuffOnPostLoad()
//    {
//        dd('test');
//    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }


    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }


    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
        }

        return $this;
    }

    public function getList(): ?CriterionsList
    {
        return $this->list;
    }

    public function setList(?CriterionsList $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCriterion($this);
        }

        return $this;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->addCriterion($this);
        }

        return $this;
    }

    /**
     * @return Collection|ProgrammingMould[]
     */
    public function getProgrammingMoulds(): Collection
    {
        return $this->ProgrammingMoulds;
    }

    public function addProgrammingMould(ProgrammingMould $ProgrammingMould): self
    {
        if (!$this->ProgrammingMoulds->contains($ProgrammingMould)) {
            $this->ProgrammingMoulds[] = $ProgrammingMould;
            $ProgrammingMould->addCriterion($this);
        }

        return $this;
    }

    public function removeProgrammingMould(ProgrammingMould $ProgrammingMould): self
    {
        if ($this->ProgrammingMoulds->contains($ProgrammingMould)) {
            $this->ProgrammingMoulds->removeElement($ProgrammingMould);
            $ProgrammingMould->removeCriterion($this);
        }

        return $this;
    }

    /**
     * @return Collection|LocalProgramming[]
     */
    public function getLocalProgrammings(): Collection
    {
        return $this->LocalProgrammings;
    }

    public function addLocalProgramming(LocalProgramming $LocalProgramming): self
    {
        if (!$this->LocalProgrammings->contains($LocalProgramming)) {
            $this->LocalProgrammings[] = $LocalProgramming;
            $LocalProgramming->addCriterion($this);
        }

        return $this;
    }

    public function removeLocalProgramming(LocalProgramming $LocalProgramming): self
    {
        if ($this->LocalProgrammings->contains($LocalProgramming)) {
            $this->LocalProgrammings->removeElement($LocalProgramming);
            $LocalProgramming->removeCriterion($this);
        }

        return $this;
    }

}
