<?php


namespace App\Service;


use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PaginatorService implements PaginatorInterface
{

    /**
     * @var PaginatorInterface $paginator
     */
    private $paginator;


    /**
     * PaginatorService constructor.
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }


    /**
     * @param mixed $target
     * @param int $page
     * @param int $limit
     * @param array $options
     * @return PaginationInterface
     */
    public function paginate($target, int $page = 1, int $limit = 10, array $options = []): PaginationInterface
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }
}