<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\DisplaySettingRepository")
 * @ORM\Table(name="display_settings")
 */
class DisplaySetting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $screensQuantity;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="DisplaySpace", inversedBy="displaySettings", cascade={"persist"})
     * @ORM\JoinColumn(name="display_space_id", referencedColumnName="id")
     */
    private $displaySpace;




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

    public function getScreensQuantity(): ?int
    {
        return $this->screensQuantity;
    }

    public function setScreensQuantity(int $screensQuantity): self
    {
        $this->screensQuantity = $screensQuantity;

        return $this;
    }

    public function getDisplaySpace(): ?DisplaySpace
    {
        return $this->displaySpace;
    }

    public function setDisplaySpace(?DisplaySpace $displaySpace): self
    {
        $this->displaySpace = $displaySpace;

        return $this;
    }
}
