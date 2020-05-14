<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use ORM\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ExpectedChangeRepository")
 */
class ExpectedChange
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
    private $entity;

    /**
     * @return mixed
     */
    public function getEntityObject()
    {
        return $this->entityObject;
    }

    /**
     * @param mixed $entityObject
     */
    public function setEntityObject($entityObject): void
    {
        $this->entityObject = $entityObject;
    }

    private $entityObject;

    /**
     * @ORM\Column(type="integer")
     */
    private $instance;

    /**
     * @ORM\Column(type="json")
     */
    private $datas = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Date")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expectedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $requestedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): ?int
    {
        return $this->entity;
    }

    public function setEntity(int $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getInstance(): ?int
    {
        return $this->instance;
    }

    public function setInstance(int $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    public function getDatas(): ?array
    {
        return $this->datas;
    }

    public function setDatas(array $datas): self
    {
        $this->datas = $datas;

        return $this;
    }

    public function getExpectedAt(): ?Date
    {
        return $this->expectedAt;
    }

    public function setExpectedAt(Date $expectedAt): self
    {
        $this->expectedAt = $expectedAt;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }
}
