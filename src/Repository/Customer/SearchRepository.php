<?php


namespace App\Repository\Customer;


use App\Entity\Search;
use App\Repository\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Search|null find($id, $lockMode = null, $lockVersion = null)
 * @method Search|null findOneBy(array $criteria, array $orderBy = null)
 * @method Search[]    findAll()
 * @method Search[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRepository extends ServiceEntityRepository
{

    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Search::class);
    }

    /**
     * @param Search $search
     * @return object[]
     */
    public function paginate(Search $search): array
    {

        $query = $this->createQueryBuilder('c');

        foreach ($search->getColumns() as $column => $value)
        {
            $query = $query->andWhere('c.' . $column . " = :" . $column)->setParameter($column, $value);
        }

        return $query->getQuery()->getArrayResult();

    }

}