<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\FfmpegTasksRepository")
 * @ORM\Table(name="ffmpeg_tasks")
 */
class FfmpegTasks
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="uploadTasks")
     * @ORM\JoinColumn(nullable=false, name="customer_id")
     */
    private $customer;

    /**
     * @ORM\Column(type="string", name="file_name", length=100)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", name="file_type", length=15)
     */
    private $filetype;

    /**
     * @ORM\Column(type="string", name="media_type", length=4)
     */
    private $mediatype;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $started;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finished;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $errors;

    /**
     * @ORM\Column(type="json")
     */
    private $media = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFiletype(): ?string
    {
        return $this->filetype;
    }

    public function setFiletype(string $filetype): self
    {
        $this->filetype = $filetype;

        return $this;
    }

    public function getMediatype(): ?string
    {
        return $this->mediatype;
    }

    public function setMediatype(string $mediatype): self
    {
        $this->mediatype = $mediatype;

        return $this;
    }

    public function getRegistered(): ?\DateTimeInterface
    {
        return $this->registered;
    }

    public function setRegistered(?\DateTimeInterface $registered): self
    {
        $this->registered = $registered;

        return $this;
    }

    public function getStarted(): ?\DateTimeInterface
    {
        return $this->started;
    }

    public function setStarted(?\DateTimeInterface $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getFinished(): ?\DateTimeInterface
    {
        return $this->finished;
    }

    public function setFinished(?\DateTimeInterface $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getErrors(): ?string
    {
        return $this->errors;
    }

    public function setErrors(string $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getMedia(): ?array
    {
        return $this->media;
    }

    public function setMedia(array $media): self
    {
        $this->media = $media;

        return $this;
    }

}
