<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin\Screen ;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\SiteScreenRepository")
 * @ORM\Table(name="sites_screens")
 */
class SiteScreen
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="siteScreens")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $screenId;

    private $screen ;

    /**
     * One Product has One Shipment.
     * @ORM\OneToOne(targetEntity="LocalProgramming", cascade={"persist"})
     * @ORM\JoinColumn(name="local_programming_id", referencedColumnName="id")
     */
    private $localProgramming;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setScreen( Screen $screen ):self {

        $this->screen = $screen ;
//        $this->site->setScreen()
        $this->setScreenId($this->screen->getId()) ;

        return $this;
    }

    public function getScreen() : ?Screen {
        return $this->screen ;
    }

    public function getScreenId(): ?int
    {
        return $this->screenId;
    }

    public function setScreenId(int $screenId): self
    {
        $this->screenId = $screenId;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function getLocalProgramming(): ?LocalProgramming
    {
        return $this->localProgramming;
    }

    public function setLocalProgramming(?LocalProgramming $localProgramming): self
    {
        $this->localProgramming = $localProgramming;

        return $this;
    }

}
