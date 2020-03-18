<?php

namespace App\Entity\Admin;

use App\Entity\Admin\Action;
use App\Entity\Admin\Role;
use App\Entity\Admin\Subject;
use App\Entity\Admin\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\PermissionRepository")
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="permission")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="permission")
     * @ORM\JoinTable(name="user_permission",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $users;


    /**
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="permissions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="permissions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $subject;
    


    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addPermission($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $role->removePermission($this);
        }

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
            $user->addPermission($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removePermission($this);
        }

        return $this;
    }

    public function getAction(): ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }


}
