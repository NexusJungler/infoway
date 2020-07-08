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
     * @ORM\ManyToMany(targetEntity="ProgrammingMould", mappedBy="tags")
     */
    private $ProgrammingMoulds;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="LocalProgramming", mappedBy="tags")
     */
    private $LocalProgrammings;

    /**
     * @ORM\ManyToMany(targetEntity="Media", mappedBy="tags", cascade={"refresh"})
     */
    private $medias ;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProgrammingMoulds = new ArrayCollection() ;
        $this->LocalProgrammings = new ArrayCollection() ;
        $this->medias = new ArrayCollection();
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
            $ProgrammingMould->addTag($this);
        }

        return $this;
    }

    public function removeProgrammingMould(ProgrammingMould $ProgrammingMould): self
    {
        if ($this->ProgrammingMoulds->contains($ProgrammingMould)) {
            $this->ProgrammingMoulds->removeElement($ProgrammingMould);
            $ProgrammingMould->removeTag($this);
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
            $LocalProgramming->addTag($this);
        }

        return $this;
    }

    public function removeLocalProgramming(LocalProgramming $LocalProgramming): self
    {
        if ($this->LocalProgrammings->contains($LocalProgramming)) {
            $this->LocalProgrammings->removeElement($LocalProgramming);
            $LocalProgramming->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    public function replaceMedia(Media $mediaToReplace, Media $substitute)
    {
        if ($this->medias->contains($mediaToReplace)) {
            $this->medias->removeElement($mediaToReplace);
        }

        if (!$this->medias->contains($substitute)) {
            $this->medias[] = $substitute;
        }

        return $this;
    }


}