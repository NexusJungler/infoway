<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserRolesRepository")
 */
class UserRoles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @ORM\Column(type="integer")
     */
    private $customerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleId(): ?int
    {
        return $this->role;
    }

    public function setRoleId(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCustomerRole(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerRole(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
