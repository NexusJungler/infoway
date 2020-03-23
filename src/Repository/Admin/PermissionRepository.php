<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Permission;
use App\Entity\Admin\Role;
use App\Entity\Admin\User;
use App\Service\ArraySearchRecursiveService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{


    private ArraySearchRecursiveService $__searchRecursiveService;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
        $this->__searchRecursiveService = new ArraySearchRecursiveService();
    }


    /**
     * Remove duplicate value in array
     *
     * @param array $array recursive or not array
     * @return array cleaned array
     */
    private function removeDuplicateDataFromArray(array $array)
    {

        // @see: https://www.php.net/manual/en/function.array-unique
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value)
        {
            if ( is_array($value) )
                $result[$key] = $this->removeDuplicateDataFromArray($value);
        }

        return $result;
    }


    public function createNewDatabaseAccessPermission(string $databaseName, array $admins): bool
    {

        $permission = new Permission();
        $permission->setName("Accéder à la base '" . $databaseName. "'")
                    // give access on this permission to God role
                   ->addRole($this->getEntityManager()->getRepository(Role::class)->findOneByName('God'));

        foreach ($admins as $admin)
        {
            $permission->addUser($admin);

            $this->getEntityManager()->persist($admin);
        }

        $this->getEntityManager()->persist($permission);
        $this->getEntityManager()->flush();

        return true;
    }


    // /**
    //  * @return Permission[] Returns an array of Permission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
