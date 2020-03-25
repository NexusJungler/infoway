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
}
