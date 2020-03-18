<?php


namespace App\Service;


use App\Entity\Admin\{Permission, Role, User};
use App\Repository\Admin\{ PermissionRepository, UserRepository };
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\{ EntityManager, EntityRepository };

class PermissionsHandler
{


    /**
     * @var EntityManager
     */
    private EntityManager $__manager;


    /**
     * @var UserRepository|ObjectRepository|EntityRepository
     */
    private $__userRepository;


    /**
     * @var PermissionRepository|ObjectRepository|EntityRepository
     */
    private $__permissionRepository;


    public function __construct(EntityManager $manager)
    {
        $this->__manager = $manager;
        $this->__userRepository = $manager->getRepository(User::class);
        $this->__permissionRepository = $manager->getRepository(Permission::class);
    }


    public function getUserPermissions(User $user, bool $onlyIds = false): array
    {
        return $this->__userRepository->getUserPermissions($user, $onlyIds);
    }


    public function getUserRolePermissions(User $user, bool $onlyIds = false)
    {
        return $this->__userRepository->getUserRolePermissions($user, $onlyIds);
    }

}