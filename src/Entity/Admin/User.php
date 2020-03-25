<?php


namespace App\Entity\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserRepository")
 */
class User
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=30, name="first_name")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30, name="last_name")
     */
    private $lastName;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phoneNumber;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordResetToken;

    /**
     * @ORM\Column(type="date", length=255, nullable=true)
     */
    private $requestedPasswordAt;

    /**
     * @ORM\Column(type="date", length=255)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", length=1)
     */
    private $activated;

    /**
     * Many Users have Many Groups.
     * @ManyToMany(targetEntity="Customer", inversedBy="users")
     * @JoinTable(name="users_customers")
     */
    private $customers;

    /**
     * One user has many roles. This is the inverse side.
     * @ORM\OneToMany(targetEntity="UserRoles", mappedBy="user")
     */
    private $roles;

    /**
     * Many Users have Many Groups.
     * @MOR\ManyToMany(targetEntity="Permission")
     * @ORM\JoinTable(name="users_permissions",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *      )
     */
    private $permissions;
    /**
     * @ORM\ManyToOne(targetEntity="Perimeter")
     * @ORM\JoinColumn(name="perimeter_id", referencedColumnName="id")
     */
    private $perimeter;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
     * @return Collection|UserRoles[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(UserRoles $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->setUser($this);
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

    public function getPerimeter(): ?Perimeter
    {
        return $this->perimeter;
    }

    public function setPerimeter(?Perimeter $perimeter): self
    {
        $this->perimeter = $perimeter;

        return $this;
    }



}