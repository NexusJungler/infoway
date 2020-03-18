<?php


namespace App\Entity;


class Search
{

    private $entity;
    private $columns;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }


    public function getColumns()
    {
        return $this->columns;
    }

}