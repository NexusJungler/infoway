<?php

namespace App\Entity\Customer;

use App\Entity\Admin\Allergen;
use App\Entity\Customer\ProductAllergen;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="PriceType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $price_type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $end;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Customer\Tag", inversedBy="products")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="Criterion", inversedBy="products")
     * @ORM\JoinTable(name="products_criterions")
     */
    private $criterions;

    /**
     * @ORM\OneToMany(targetEntity="ProductAllergen", mappedBy="product", cascade={"persist"})
     */
    private $product_allergens;

    /**
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="products")
     * @ORM\JoinTable(name="products_medias")
     */
    private $medias;


    private $allergens;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->criterions = new ArrayCollection();
        $this->allergens = new ArrayCollection();
        $this->product_allergens = new ArrayCollection();
        $this->medias = new ArrayCollection() ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function resetId(): self
    {
        $this->id = null;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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

    public function getPriceType(): ?PriceType
    {
        return $this->price_type;
    }

    public function setPriceType(?PriceType $price_type): self
    {
        $this->price_type = $price_type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $date): self
    {
        $this->createdAt = $date;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

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
            $criterion->addProduct($this);
        }

        return $this;
    }

    public function removeCriterion(Criterion $criterion): self
    {
        if ($this->criterions->contains($criterion)) {
            $this->criterions->removeElement($criterion);
            $criterion->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|ProductAllergen[]
     */
    public function getProductAllergens(): Collection
    {
        return $this->product_allergens;
    }

    public function addProductAllergen(ProductAllergen $product_allergen): self
    {
        if (!$this->product_allergens->contains($product_allergen)) {
            $this->product_allergens[] = $product_allergen;
        }

        return $this;
    }

    public function removeProductAllergen(ProductAllergen $allergen): self
    {
        if ($this->tags->contains($allergen)) {
            $this->tags->removeElement($allergen);
        }

        return $this;
    }

    /**
     * @return Collection|Allergen[]
     */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function setAllergens(Collection $allergens): self
    {
        $this->allergens = $allergens;
        return $this;
    }

    public function addAllergen(Allergen $allergen): self
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens[] = $allergen;
            $new = new ProductAllergen();
            $new->setAllergenId($allergen->getId())
                ->setProduct($this);
            $this->addProductAllergen($new);
        }

        return $this;
    }

    public function removeAllergen(Allergen $allergen): self
    {
        if ($this->tags->contains($allergen)) {
            $this->tags->removeElement($allergen);
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


}
