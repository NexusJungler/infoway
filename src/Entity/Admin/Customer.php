<?php


namespace App\Entity\Admin;


use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Customer\Role ;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\CustomerRepository")
 *
 * Unique name
 * @UniqueEntity("name")
 */
class Customer
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $name;

    //Cette propriete sert a contenir tous les sites qu un user possede, contenu dans l objet Customer representant l enseigne contenant le site. Cela permettra d avoir un objet user qui contiendra des enseignes qui contiendront des sites
    private $sites ;


    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $logo;


    /**
     * Many Customers have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="customers")
     */
    private $users;

    /**
     * One Customer has One Cart.
     * @ORM\OneToOne(targetEntity="Contact", mappedBy="customer", cascade={"persist"})
     */
    private $contact;

    /**
     * Une enseigne se situe dans un pays qui quant a lui peut apparaitre dans plusieurs enseignes
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;


    /**
     * Une enseigne possede une timezone qui quant a elle peut apparaitre dans plusieurs enseignes
     * @ORM\ManyToOne(targetEntity="TimeZone")
     * @ORM\JoinColumn(name="timezone_id", referencedColumnName="id")
     */
    private $timeZone;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->sites = new ArrayCollection();

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCustomer($this);
        }

        return $this;
    }

    /**
     * @param string $sites
     */
    public function setSites(ArrayCollection $sites): void
    {
        $this->sites = $sites;
    }

    /**
     * @return ArrayCollection
     */
    public function getSites(): ArrayCollection
    {
        return $this->sites;
    }


    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeCustomer($this);
        }

        return $this;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site) ) {
            $this->sites->removeElement($site);
//            $site->setCustomer(null);
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getTimeZone(): ?TimeZone
    {
        return $this->timeZone;
    }

    public function setTimeZone(?TimeZone $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        // set (or unset) the owning side of the relation if necessary
        $newCustomer = null === $contact ? null : $this;
        if ($contact->getCustomer() !== $newCustomer) {
            $contact->setCustomer($newCustomer);
        }

        return $this;
    }


}