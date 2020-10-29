<?php

namespace App\Repository\Customer;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use App\Repository\RepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * @method product|null find($id, $lockMode = null, $lockVersion = null)
 * @method product|null findOneBy(array $criteria, array $orderBy = null)
 * @method product[]    findAll()
 * @method product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, product::class);
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


    public function findProductsAssociatedDatas(string $data = 'tags')
    {

        $productsAssociatedDatas = [];
        foreach ($this->findAll() as $product)
        {

            $productsAssociatedDatas[$product->getId()] = [
                'ids' => [],
                'names' => [],
            ];

            if($data === 'tags')
            {

                foreach ($product->getTags()->getValues() as $tag)
                {

                    /*if(!in_array($tag, $productsAssociatedDatas))
                        $productsAssociatedDatas[] = $tag;*/

                    $productsAssociatedDatas[$tag->getId()]['ids'][] = $tag->getId();
                    $productsAssociatedDatas[$tag->getId()]['names'][] = $tag->getName();
                }

            }

            elseif ($data === 'criterions')
            {

                foreach ($product->getCriterions()->getValues() as $criterion)
                {

                    /*if(!in_array($criterion, $productsAssociatedDatas))
                        $productsAssociatedDatas[] = $criterion;*/

                    $productsAssociatedDatas[$product->getId()]['ids'][] = $criterion->getId();
                    $productsAssociatedDatas[$product->getId()]['names'][] = $criterion->getName();
                }

            }

            else
                throw new Exception(sprintf("Unrecognized parameter value ! Expected 'tags' or 'criterions', but '%s' given ", $data));


        }

        //dd($productsAssociatedDatas);

        return $productsAssociatedDatas;
    }


    public function getAllProductsWhereCriterionDoesNotAppear(Criterion $criterion){

        $criterionProductsIds = $criterion->getProducts()->filter(function(Product $product){
            return $product->getId() ;
        });

        return $criterionProductsIds->count() <1 ? $this->findAll() : $this->getProductsWhereIdNotIn($criterionProductsIds->getValues() );
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

    public function getProductsCriterionsIds($productOrId)
    {

        $criterionsIds = [];

        if(is_int($productOrId))
        {
            $product = $this->find($productOrId);
            if(!$product)
                throw new Exception(sprintf("Not product found with '%s' id !", $productOrId));

            foreach ($product->getCriterions()->getValues() as $criterion)
            {
                $criterionsIds[] = $criterion->getId();
            }


        }
        else
        {

            foreach ($productOrId->getCriterions()->getValues() as $criterion)
            {
                $criterionsIds[] = $criterion->getId();
            }

        }

        return $criterionsIds;
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