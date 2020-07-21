<?php


namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ProductAllergenRepository")
 * @ORM\Table(name="product_allergen")
 */
class ProductAllergen
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="allergens")
     */
    private $product;

    /**
     * @ORM\Column(name="allergen_id",type="integer")
     */
    private $allergenId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAllergenId(): ?int
    {
        return $this->allergenId;
    }

    public function setAllergenId(int $allergenId): self
    {
        $this->allergenId = $allergenId;
        return $this;
    }


}