<?php


namespace App\Repository;


use Doctrine\Persistence\ObjectManager;

trait MainRepository
{

    public function setEntityManager(ObjectManager $entityManager): self
    {
        $this->_em = $entityManager;
        return $this;
    }

}