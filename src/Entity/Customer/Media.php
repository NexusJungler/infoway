<?php

namespace App\Entity\Customer;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\MediaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="media_type", type="string")
 * @ORM\DiscriminatorMap({ "media" = "Media", "image" = "Image", "video" = "Video", "element_graphic" = "ElementGraphic" })
 * @UniqueEntity(fields={"name"})
 */
class Media
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
     * @ORM\Column(type="string", length=255)
     */
    private $size;

    /**
     * @ORM\Column(type="datetime", name="created_at", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", name="diffusion_start" ,nullable=false)
     */
    private $diffusionStart;

    /**
     * @ORM\Column(type="date", name="diffusion_end" ,nullable=false)
     */
    private $diffusionEnd;

    /**
     * @ORM\Column(type="string", name="ratio", length=255)
     */
    private $ratio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $extension;

    /**
     * @ORM\Column(type="smallint")
     */
    private $height;

    /**
     * @ORM\Column(type="smallint")
     */
    private $width;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Customer\Tag", inversedBy="media")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Customer\Product", inversedBy="media")
     */
    private $products;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=false, name="mime_type")
     */
    private $mimeType;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_archived")
     */
    private $isArchived;

    /**
     * @ORM\Column(type="string", nullable=false, name="orientation")
     */
    private $orientation;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $containIncruste;

    /**
     * @ORM\Column(type="boolean")
     */
    private $diffusable = false;

    /**
     * @ORM\ManyToMany(targetEntity="Incruste", inversedBy="media")
     */
    private $incrustes;


    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->tags = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->incrustes = new ArrayCollection();
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDiffusionStart(): ?\DateTimeInterface
    {
        return $this->diffusionStart;
    }

    public function setDiffusionStart(?\DateTimeInterface $diffusionStart): self
    {
        $this->diffusionStart = $diffusionStart;

        return $this;
    }

    public function getDiffusionEnd(): ?\DateTimeInterface
    {
        return $this->diffusionEnd;
    }

    public function setDiffusionEnd(?\DateTimeInterface $diffusionEnd): self
    {
        $this->diffusionEnd = $diffusionEnd;

        return $this;
    }

    public function getRatio(): ?string
    {
        return $this->ratio;
    }

    public function setRatio(string $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

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
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getIsArchived()
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
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

    public function getDiffusable(): ?bool
    {
        return $this->diffusable;
    }

    public function setDiffusable(bool $diffusable): self
    {
        $this->diffusable = $diffusable;

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
