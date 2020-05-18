<?php

namespace App\Service;

use App\Entity\Customer\Criterion;
use App\Entity\Customer\Product;
use App\Entity\Customer\Site;
use App\Errors\Customer\ExistingCriterionNameError;
use App\Errors\Customer\NotExistingProductsError;
use App\Errors\Customer\NotExistingSitesError;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Proxies\__CG__\App\Entity\Customer\CriterionList;

class CriterionsHandler
{

    private $_criterionsRepo;
    private $_contactHandler;
    private $_flashBagHandler;
    private $_databaseHandler;
    private $_logoDir;
    private $_em;
    private $_allCriterionsListsNamesFromDB = [];
    private $_registry;

    public function __construct(FlashBagHandler $flashBagHandler, DatabaseAccessHandler $databaseHandler, ManagerRegistry $registry)
    {
        $this->_minimumCriterionsInlist = 2;
        $this->_registry = $registry;
        $this->_flashBagHandler = $flashBagHandler;
        $this->_databaseHandler = $databaseHandler;
    }

    public function setWorkingEntityManager(ObjectManager $em){
        $this->_em = $em ;
        $this->_criterionsRepo = $this->_em->getRepository( Criterion::class ) ;
    }

    public function isCriterionNameAlreadyExistInDb( Criterion $criterion , bool $excludeCriterionInArgument = false ) : bool {

        $criterionFoundedInDb = ! $excludeCriterionInArgument ? $this->_criterionsRepo->findOneByName( $criterion->getName() ) : $this->_criterionsRepo->getAnotherCriterionWithSameName( $criterion );

        if( ! $criterionFoundedInDb instanceof Criterion ) { return false ; }
        else {
            $error = new ExistingCriterionNameError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;
            return true ;
        }
    }

    public function isAllSitesSelectedExistsInDB( Criterion $criterion ) : bool {

        $allSitesIdsInDB  = $this->_em->getRepository(Site::class)->getAllSitesIds() ;

        $criterionsNotExistingInDb = $criterion->getSites()->filter( function(Site $siteInCriterion ) use ( $allSitesIdsInDB )  {
            return !in_array( $siteInCriterion->getId() , $allSitesIdsInDB  ) ;
        }) ;

        if( $criterionsNotExistingInDb->count() < 1 ){ return true ; }
        else {
            $error = new NotExistingSitesError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

            return false ;
        }
    }

    public function isAllProductsSelectedExistsInDB( Criterion $criterion ) : bool {

        $allProductsInDB  = $this->_em->getRepository(Product::class)->getAllProductsIds() ;

        $productsNotExistingInDb = $criterion->getProducts()->filter( function(Product $productInCriterion ) use ( $allProductsInDB )  {
            return !in_array( $productInCriterion->getId() , $allProductsInDB  ) ;
        }) ;

        if( $productsNotExistingInDb->count() < 1 ){ return true ; }
        else {
            $error = new NotExistingProductsError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

            return false ;
        }
    }



}
