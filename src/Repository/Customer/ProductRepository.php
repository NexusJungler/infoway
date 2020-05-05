<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use App\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function getAllProductsWhereCriterionDoesNotAppear(Criterion $criterion){

        $criterionProductsIds = $criterion->getProducts()->filter(function(Site $site){
            return $site->getId() ;
        });

        return $criterionProductsIds->count() <1 ? $this->findAll() : $this->getProductsWhereIdNotIn($criterionProductsIds->getValues() );
    }

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    public function getProductsWhereIdNotIn(array $ids){

        return  $this->createQueryBuilder('p')
            ->where('p.id NOT IN ( :ids )')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllProductsIds() : array {

        return array_map(
            function( Product $product ){ return $product->getId() ; }
            ,  $this->findAll() ) ;

    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
