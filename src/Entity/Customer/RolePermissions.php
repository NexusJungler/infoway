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
    private $permission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermission(): ?int
    {
        return $this->permission;
    }

    public function setPermissionId(int $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}
