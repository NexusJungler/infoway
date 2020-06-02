<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\ImageRepository")
 */
class Image extends Media
{

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $containIncruste;

    public function getContainIncruste(): bool
    {
        return $this->containIncruste;
    }

    public function setContainIncruste(bool $containIncruste): self
    {
        $this->containIncruste = $containIncruste;

        return $this;
    }

}
