<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\IncrusteRepository")
 */
class Incruste
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Media", mappedBy="incrustes")
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="incrustes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", nullable=false, name="type_incruste")
     */
    private $typeIncruste;

    /**
     * @ORM\Column(type="string", nullable=false, name="type_price")
     */
    private $typePrice;

    /**
     * @ORM\Column(type="smallint", name="x", length=10, options={"unsigned"=true})
     */
    private $x;

    /**
     * @ORM\Column(type="smallint", name="y", length=10, options={"unsigned"=true})
     */
    private $y;

    /**
     * @ORM\Column(type="smallint", length=10, options={"unsigned"=true})
     */
    private $width;

    /**
     * @ORM\Column(type="smallint", length=5, options={"unsigned"=true})
     */
    private $height;

    /**
     * @ORM\Column(type="smallint", length=5, nullable=true, options={"unsigned"=true}, name="frame_start")
     */
    private $frameStart;

    /**
     * @ORM\Column(type="smallint", length=6, nullable=true, options={"unsigned"=true}, name="frame_end")
     */
    private $frameEnd;

    /**
     * @ORM\OneToOne(targetEntity="Model", cascade={"persist", "remove"})
     */
    private $style;

    /**
     * @ORM\Column(type="integer", nullable=true, length=10)
     */
    private $visibility;

    /**
     * @ORM\Column(type="integer", nullable=true, length=10, name="category_product")
     */
    private $categoryProduct;


    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeIncruste(): string
    {
        return $this->typeIncruste;
    }

    public function setTypeIncruste(string $type): self
    {
        $this->typeIncruste = $type;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->addIncruste($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->contains($medium)) {
            $this->media->removeElement($medium);
            $medium->removeIncruste($this);
        }

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getTypePrice(): ?string
    {
        return $this->typePrice;
    }

    public function setTypePrice(string $priceType): self
    {
        $this->typePrice = $priceType;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getFrameStart(): ?int
    {
        return $this->frameStart;
    }

    public function setFrameStart(?int $frameStart): self
    {
        $this->frameStart = $frameStart;

        return $this;
    }

    public function getFrameEnd(): ?int
    {
        return $this->frameEnd;
    }

    public function setFrameEnd(?int $fameEnd): self
    {
        $this->frameEnd = $fameEnd;

        return $this;
    }

    public function getStyle(): ?Model
    {
        return $this->style;
    }

    public function setStyle(?Model $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function setVisibility(?int $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getCategoryProduct(): ?int
    {
        return $this->categoryProduct;
    }

    public function setCategoryProduct(?int $categoryProduct): self
    {
        $this->categoryProduct = $categoryProduct;

        return $this;
    }

}
