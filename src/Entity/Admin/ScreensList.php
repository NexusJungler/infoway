<?php

namespace App\Entity\Admin;

use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


class ScreensList
{
    private $screensList;


    public function __construct()
    {
        $this->screensList = new ArrayCollection();
    }

    public function addScreen(Screen $screen): self
    {
        if (!$this->screensList->contains($screen)) {
            $this->screensList[] = $screen;
        }

        return $this;
    }

    public function removeScreen(Screen $screen): self
    {
        if ($this->screensList->contains($screen)) {
            $this->screensList->removeElement($screen);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getScreensList(): ArrayCollection
    {
        return $this->screensList;
    }


}