<?php


namespace App\Entity\Admin;

use App\Entity\Customer\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use \App\Entity\Customer\Role ;



/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, name="first_name")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30, name="last_name")
     */
    private $lastName;

    /**
     * @ORM\Column(name="phone_number",type="string", length=30, nullable=true)
     */
    private $phoneNumber;


    /**
     * @ORM\Column(name="password_reset_tocket",type="string", length=255, nullable=true)
     */
    private $passwordResetToken;

    /**
     * @ORM\Column(name="requested_password_at",type="datetime", length=255, nullable=true)
     */
    private $requestedPasswordAt;

    /**
     * @ORM\Column(name="created_at",type="datetime", length=255)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", length=1)
     */
    private $activated;

    /**
     * Many Users have Many Customers.
     * @ORM\ManyToMany(targetEntity="Customer", inversedBy="users")
     * @ORM\JoinTable(name="users_customers")
     */
    private $customers;

    /**
     * One user has many roles. This is the inverse side.
     * @ORM\OneToMany(targetEntity="UserRoles", mappedBy="user")
     */
    private $userRoles;

    /**
     * One user has many roles. This is the inverse side.
     * L uerinterface a besoin que la propriete  roles contiennent un  tableau contents des objets Role
     */
    private $roles = [];

    /**
     * One user has many roles. This is the inverse side.
     * @ORM\OneToMany(targetEntity="UserPermission", mappedBy="user")
     */
    private $permissions;


    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="UserSites", mappedBy="user")
     */
    private $sitesIds;

    private $sites;

    /**
     * @ORM\ManyToOne(targetEntity="Perimeter")
     * @ORM\JoinColumn(name="perimeter_id", referencedColumnName="id")
     */
    private $perimeter;


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->sitesIds = new ArrayCollection();
        $this->sites = new ArrayCollection();

        $this->setCreatedAt(new \DateTime());
        $this->setActivated(0);
        $this->userRoles = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role,$customer): self
    {
        $customer->setRole( $role );
        if( !in_array( $customer, $this->roles) ){
            $this->roles[] = $customer ;
        }

        return $this;
    }

    public function removeRole(UserRoles $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            // set the owning side to null (unless already changed)
            if ($role->getUser() === $this) {
                $role->setUser(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getRequestedPasswordAt(): ?\DateTimeInterface
    {
        return $this->requestedPasswordAt;
    }

    public function setRequestedPasswordAt(?\DateTimeInterface $requestedPasswordAt): self
    {
        $this->requestedPasswordAt = $requestedPasswordAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
        }

        return $this;
    }

    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
        }

        return $this;
    }

    /**
     * @return Collection|UserSites[]
     */
    public function getSitesIds(): Collection
    {
        return $this->sitesIds;
    }

    public function addSitesId(UserSites $sitesId): self
    {
        if (!$this->sitesIds->contains($sitesId)) {
            $this->sitesIds[] = $sitesId;
            $sitesId->setUser($this);
        }

        return $this;
    }

    public function removeSitesId(UserSites $sitesId): self
    {
        if ($this->sitesIds->contains($sitesId)) {
            $this->sitesIds->removeElement($sitesId);
            // set the owning side to null (unless already changed)
            if ($sitesId->getUser() === $this) {
                $sitesId->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSites(): ArrayCollection
    {
        return $this->sites;
    }

    /**
     * @param ArrayCollection $sites
     */
    public function setSites(ArrayCollection $sites): void
    {
        $this->sites = $sites;
    }

    //fonction qui permet d ajouter un objet site recupere d une base d un client a un utilisateur  . Il faut preciser l enseigne c est celle ci qui contiendra tous les sites de l user afin qu ils soient rangés par enseigne
    public function addSite(Site $site, Customer $customer) {
        if( ! $customer->getSites()->contains( $site ) ){
            $customer->addSite( $site );
        }
        if( $customer->getSites()->contains($site) ){
            if( ! $this->sites->contains( $customer ) ){
                $this->sites[] = $customer ;
            }
        }
    }

    //TODO permet de supprimer un site
    public function removeSite(Site $site): self
    {
        if ($this->sites->contains($site)) {
//            $this->sites->removeElement($site);
        }

        return $this;
    }

    public function getPerimeter(): ?Perimeter
    {
        return $this->perimeter;
    }

    public function setPerimeter(?Perimeter $perimeter): self
    {
        $this->perimeter = $perimeter;

        return $this;
    }

    /**
     * @return Collection|UserRoles[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRoles $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->setUser($this);
        }

        return $this;
    }

    public function removeUserRole(UserRoles $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            // set the owning side to null (unless already changed)
            if ($userRole->getUser() === $this) {
                $userRole->setUser(null);
            }
        }

        return $this;
    }

}