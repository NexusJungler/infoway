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

    //Cette propriete sert a contenir le role appartenant a un user stocké dans l enseigne auquel il appartient
    private $role;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * Many Customers have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="customers")
     */
    private $users;


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

    /**
     * @return mixed
     */
    public function getRole():?Role
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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


}