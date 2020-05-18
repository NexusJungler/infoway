<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Product;
use App\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method product|null find($id, $lockMode = null, $lockVersion = null)
 * @method product|null findOneBy(array $criteria, array $orderBy = null)
 * @method product[]    findAll()
 * @method product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, product::class);
    }

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    public function findProductWithTags($id)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.tags', 't')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findProductsCriterions()
    {

        $productsCriterions = [];

        foreach ($this->findAll() as $product)
        {

            $productsCriterions[$product->getName()] = [];

            foreach ($product->getCriterions()->getValues() as $criterion)
            {
                $productsCriterions[$product->getName()][] = $criterion->getName();
            }

        }

        return $productsCriterions;

    }

    // Eureka --> Surcharger la Méthode findAll() pour contrôler le lazy load !!

    // /**
    //  * @return product[] Returns an array of product objects
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
    public function findOneBySomeField($value): ?product
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
