<?php


namespace App\Entity\Customer;


use Doctrine\Common\Collections\ArrayCollection;

class RoleList
{

    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }


    public function setRoles(ArrayCollection $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }


    public function getRoles(): ArrayCollection
    {
        return $this->roles;
    }

}