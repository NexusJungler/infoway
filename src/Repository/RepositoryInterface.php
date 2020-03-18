<?php


namespace App\Repository;


use Doctrine\Persistence\ObjectManager;

interface RepositoryInterface
{

    public function setEntityManager(ObjectManager $entityManager): self;

}