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
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Product", inversedBy="incrustes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=false, name="price_type")
     */
    private $priceType;

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


    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getPriceType(): ?string
    {
        return $this->priceType;
    }

    public function setPriceType(string $priceType): self
    {
        $this->priceType = $priceType;

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

}
