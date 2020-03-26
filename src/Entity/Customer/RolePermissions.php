<?php

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Customer\RolePermissionsRepository")
 */
class RolePermissions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $permissionId;

    private $permission ;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="permissions")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermissionId(): ?int
    {
        return $this->permissionId;
    }

    public function setPermissionId(int $permissionId): self
    {
        $this->permissionId = $permissionId;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}
