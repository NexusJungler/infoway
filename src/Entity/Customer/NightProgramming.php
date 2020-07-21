<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\NightProgrammingRepository")
 * @ORM\Table("night_programming")
 */
class NightProgramming
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceIncrease;

    /**
     * @ORM\Column(type="time")
     */
    private $startAt;

    /**
     * @ORM\Column(type="time")
     */
    private $endAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceIncrease(): ?int
    {
        return $this->priceIncrease;
    }

    public function setPriceIncrease(int $priceIncrease): self
    {
        $this->priceIncrease = $priceIncrease;

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
