<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\TagRepository")
 * @UniqueEntity(fields="name",message="Ce nom est déjà utilisé")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="tags", cascade={"persist"})
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity="Site", mappedBy="tags", cascade={"persist"})
     */
    private $sites;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="DisplayMould", mappedBy="tags")
     */
    private $displayMoulds;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="ScreenDisplay", mappedBy="tags")
     */
    private $screenDisplays;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->displayMoulds = new ArrayCollection() ;
        $this->screenDisplays = new ArrayCollection() ;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

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
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addTag($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeTag($this);
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

    public function addSite(Site $site): self
    {

        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->addTag($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
            $site->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection|DisplayMould[]
     */
    public function getDisplayMoulds(): Collection
    {
        return $this->displayMoulds;
    }

    public function addDisplayMould(DisplayMould $displayMould): self
    {
        if (!$this->displayMoulds->contains($displayMould)) {
            $this->displayMoulds[] = $displayMould;
            $displayMould->addTag($this);
        }

        return $this;
    }

    public function removeDisplayMould(DisplayMould $displayMould): self
    {
        if ($this->displayMoulds->contains($displayMould)) {
            $this->displayMoulds->removeElement($displayMould);
            $displayMould->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection|ScreenDisplay[]
     */
    public function getScreenDisplays(): Collection
    {
        return $this->screenDisplays;
    }

    public function addScreenDisplay(ScreenDisplay $screenDisplay): self
    {
        if (!$this->screenDisplays->contains($screenDisplay)) {
            $this->screenDisplays[] = $screenDisplay;
            $screenDisplay->addTag($this);
        }

        return $this;
    }

    public function removeScreenDisplay(ScreenDisplay $screenDisplay): self
    {
        if ($this->screenDisplays->contains($screenDisplay)) {
            $this->screenDisplays->removeElement($screenDisplay);
            $screenDisplay->removeTag($this);
        }

        return $this;
    }




}