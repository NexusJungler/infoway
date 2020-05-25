<?php

namespace App\Object\Customer\Action;

use App\Entity\Customer\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class TagsAction
{

    private $tags;

    public function __construct() {
        $this->tags = new ArrayCollection();
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
           $this->tags->remove( $tag ) ;
        }

        return $this;
    }


}
