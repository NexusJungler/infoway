<?php
namespace App\Service;


use App\Entity\Admin\Customer;
use App\Entity\Customer\CriterionsList;
use App\Errors\Customer\ExistingCriterionsListNameError;
use App\Errors\Customer\MinimumCriterionsInListNonReachedError;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use App\Service\FlashBagHandler;
use Doctrine\Persistence\ObjectManager;

class CriterionsListHandlerService
{

    private $_criterionsListsRepo;
    private $_contactHandler;
    private $_flashBagHandler;
    private $_databaseHandler;
    private $_logoDir;
    private $_em;
    private ?Customer $_currentCustomer = null ;
    private $_allCriterionsListsNamesFromDB = [] ;
    private $_registry ;
    public function __construct(EntityManager $em, FlashBagHandler $flashBagHandler, DatabaseAccessHandler $databaseHandler, Registry $registry )
    {
        $this->_em = $em;
        $this->_minimumCriterionsInlist = 2 ;
        $this->_registry = $registry ;
        $this->_criterionsListsRepo = $this->_em->getRepository(CriterionsList::class);
        $this->_flashBagHandler = $flashBagHandler;
        $this->_databaseHandler = $databaseHandler;
    }

    public function setWorkingEntityManager(ObjectManager $em){
        $this->_em = $em ;
        $this->_criterionsListsRepo = $this->_em->getRepository( CriterionsList::class ) ;
    }

    public function handleCriterionsInList( CriterionsList $criterionsList ) {

        $criterionPositionInlist = 0 ;

        foreach($criterionsList->getCriterions() as $criterion){

            if ( $criterion->getName() === null ) {
                $criterionsList->removeCriterion($criterion) ;
            } else{

                $criterionPositionInlist ++ ;
                $criterion->setPosition( $criterionPositionInlist ) ;

            }

        }

        return true ;
    }

    public function isMinimumCriterionsInListLimitIsReached(CriterionsList $criterionsList ) : bool {
       if ( count ( $criterionsList->getCriterions() ) >= $this->_minimumCriterionsInlist ) { return true ; }
       else {

           $error = new MinimumCriterionsInListNonReachedError( $this->_minimumCriterionsInlist ) ;
           $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;

           return false ;
       }

    }
    public function isCriterionsListNameAlreadyExistInDb(CriterionsList $criterionsList) : bool {
        $criterionListFoundInDb  = $this->_criterionsListsRepo->findOneBy( [ 'name' => $criterionsList->getName() ] ) ;

        if ( ! $criterionListFoundInDb instanceof CriterionsList ) {

            return false ;
        }else{

            $error = new ExistingCriterionsListNameError() ;
            $this->_flashBagHandler->getFlashBag()->set('error',$error->errorToArray() ) ;
            return true;
        }
    }

    public function hydrateCriterionsListsNamesFromDB() {
        $this->_allCriterionsListsNamesFromDB = $this->_criterionsListsRepo->getAllsCriterionsListsNamesFromDB() ;
    }



}