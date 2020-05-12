<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\DisplaySettingRepository")
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
    private $screensNumber;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Display", inversedBy="displaySettings")
     * @ORM\JoinColumn(name="display_id", referencedColumnName="id")
     */
    private $playlists;


    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="MediaType")
     * @ORM\JoinTable(name="display_setting_allowed_types",
     *      joinColumns={@ORM\JoinColumn(name="display_setting_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_type_id", referencedColumnName="id")}
     *      )
     */
    private $allowedMediasTypes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    public function __construct() {
        $this->displaySpaces = new ArrayCollection();
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

    public function getScreensNumber(): ?int
    {
        return $this->screensNumber;
    }

    public function setScreensNumber(int $screensNumber): self
    {
        $this->screensNumber = $screensNumber;

        return $this;
    }




    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }
}
