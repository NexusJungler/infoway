<?php


namespace App\Service;


use App\Entity\Admin\{Permission, Role, User};
use App\Repository\Admin\{ PermissionRepository, UserRepository };
use Doctrine\ORM\{EntityManager, EntityManagerInterface, EntityRepository};

class PermissionsHandler
{


    private EntityManagerInterface $__manager;

    private UserRepository $__userRepository;

    private PermissionRepository $__permissionRepository;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->__manager = $manager;
        $this->__userRepository = $manager->getRepository(User::class);
        $this->__permissionRepository = $manager->getRepository(Permission::class);
    }


    public function getUserPermissions(User $user, bool $onlyIds = false): array
    {
        return $this->__userRepository->getUserPermissions($user, $onlyIds);
    }


    public function getUserRolePermissions(User $user, bool $onlyIds = false): array
    {
        return $this->__userRepository->getUserRolePermissions($user, $onlyIds);
    }


    public function createNewDatabaseAccessPermission(string $databaseName, array $admins): bool
    {
        return $this->__permissionRepository->createNewDatabaseAccessPermission($databaseName, $admins);
    }

}