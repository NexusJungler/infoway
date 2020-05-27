<?php

namespace App\Entity\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\VideoRepository")
 */
class Video extends Media
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $format;

    /**
     * @ORM\Column(type="string", name="sample_size", length=255)
     */
    private $sampleSize;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $encoder;

    /**
     * @ORM\Column(type="string", name="video_codec", length=255)
     */
    private $videoCodec;

    /**
     * @ORM\Column(type="string", name="video_codec_level", length=255)
     */
    private $videoCodecLevel;

    /**
     * @ORM\Column(type="string", name="video_frequence", length=255)
     */
    private $videoFrequence;

    /**
     * @ORM\Column(type="smallint", name="video_frame")
     */
    private $videoFrame;

    /**
     * @ORM\Column(type="string", name="video_debit", length=255)
     */
    private $videoDebit;

    /**
     * @ORM\Column(type="string", name="audio_codec", length=255)
     */
    private $audioCodec;

    /**
     * @ORM\Column(type="smallint", name="audio_frame", nullable=true)
     */
    private $audioFrame;

    /**
     * @ORM\Column(type="string", name="audio_debit", length=255)
     */
    private $audioDebit;

    /**
     * @ORM\Column(type="string", name="audio_frequence", length=255)
     */
    private $audioFrequence;

    /**
     * @ORM\Column(type="integer", name="audio_channel", nullable=true, length=1)
     */
    private $audioChannel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $duration;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $containIncruste;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Customer\Incruste", inversedBy="videos")
     */
    private $incrustes;

    public function __construct()
    {
        parent::__construct();
        $this->incrustes = new ArrayCollection();
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getSampleSize(): ?string
    {
        return $this->sampleSize;
    }

    public function setSampleSize(string $sampleSize): self
    {
        $this->sampleSize = $sampleSize;

        return $this;
    }

    public function getEncoder(): ?string
    {
        return $this->encoder;
    }

    public function setEncoder(string $encoder): self
    {
        $this->encoder = $encoder;

        return $this;
    }

    public function getVideoCodec(): ?string
    {
        return $this->videoCodec;
    }

    public function setVideoCodec(string $videoCodec): self
    {
        $this->videoCodec = $videoCodec;

        return $this;
    }

    public function getVideoCodecLevel(): ?string
    {
        return $this->videoCodecLevel;
    }

    public function setVideoCodecLevel(string $videoCodecLevel): self
    {
        $this->videoCodecLevel = $videoCodecLevel;

        return $this;
    }

    public function getVideoFrequence(): ?string
    {
        return $this->videoFrequence;
    }

    public function setVideoFrequence(string $videoFrequence): self
    {
        $this->videoFrequence = $videoFrequence;

        return $this;
    }

    public function getVideoFrame(): ?int
    {
        return $this->videoFrame;
    }

    public function setVideoFrame(int $videoFrame): self
    {
        $this->videoFrame = $videoFrame;

        return $this;
    }

    public function getVideoDebit(): ?string
    {
        return $this->videoDebit;
    }

    public function setVideoDebit(string $videoDebit): self
    {
        $this->videoDebit = $videoDebit;

        return $this;
    }

    public function getAudioCodec(): ?string
    {
        return $this->audioCodec;
    }

    public function setAudioCodec(string $audioCodec): self
    {
        $this->audioCodec = $audioCodec;

        return $this;
    }

    public function getAudioDebit(): ?string
    {
        return $this->audioDebit;
    }

    public function setAudioDebit(string $audioDebit): self
    {
        $this->audioDebit = $audioDebit;

        return $this;
    }

    public function getAudioFrequence(): ?string
    {
        return $this->audioFrequence;
    }

    public function setAudioFrequence(string $audioFrequence): self
    {
        $this->audioFrequence = $audioFrequence;

        return $this;
    }

    public function getAudioChannel(): ?int
    {
        return $this->audioChannel;
    }

    public function setAudioChannel(?int $audioChannel): self
    {
        $this->audioChannel = $audioChannel;

        return $this;
    }

    public function getAudioFrame(): ?int
    {
        return $this->audioFrame;
    }

    public function setAudioFrame(?int $audioFrame): self
    {
        $this->audioFrame = $audioFrame;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

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
