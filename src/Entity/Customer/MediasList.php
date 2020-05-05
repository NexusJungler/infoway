<?php


namespace App\Entity\Customer;


use Doctrine\Common\Collections\ArrayCollection;

class MediasList
{

    private $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    /**
     * @param ArrayCollection $medias
     */
    public function setMedias(ArrayCollection $medias): self
    {
        $this->medias = $medias;

        return $this;
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

    /**
     * @return ArrayCollection
     */
    public function getMedias(): ArrayCollection
    {
        return $this->medias;
    }

}