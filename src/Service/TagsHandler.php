<?php

namespace App\Service;


use App\Entity\Admin\Customer;
use App\Entity\Admin\TagsList;
use App\Entity\Admin\User;
use App\Entity\Customer\Site;
use App\Entity\Customer\Tag;
use App\Errors\Tag\TagError;
use App\Errors\TagsList\MinimumSitesNotReached;
use App\Errors\TagsList\NoTagToInsertError;
use App\Errors\TagsList\NotAllowedSitesError;
use App\Form\Customer\TagListType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class TagsHandler
{

    private $_criterionsRepo;
    private $_contactHandler;
    private $_flashBagHandler;
    private $_databaseHandler;
    private $_logoDir;
    private $_em;
    private $_allCriterionsListsNamesFromDB = [];
    private $_registry;
    private int $_minimumSitesInTags ;

    public function __construct(FlashBagHandler $flashBagHandler, DatabaseAccessHandler $databaseHandler, ManagerRegistry $registry)
    {
        $this->_minimumSitesInTags = 1 ;
        $this->_registry = $registry;
        $this->_flashBagHandler = $flashBagHandler;
        $this->_databaseHandler = $databaseHandler;
    }

    public function isMinimumSitesSelectionIsReached( TagsList $tagList ) : bool {
        if( $tagList->getSites()->count() >= $this->_minimumSitesInTags ) return true ;
        else {

            $this->_flashBagHandler->addErrorInFlashbag( new MinimumSitesNotReached(1) );
            return false ;
        }
    }

    public function isTagsListEmpty(TagsList $tagsList) : bool {
        if( $tagsList->getTags()->count() >= 1 ) { return false ; }
        else{
            $this->_flashBagHandler->addErrorInFlashbag( new NoTagToInsertError() );
            return true ;
        }

    }
    public function isAllSitesSelectedArePossessedByUser(ArrayCollection $sitesSelected , User $user, Customer $customer ) : bool {

        $customerSiteRepo = $this->_registry->getManager( $customer->getName() )->getRepository(Site::class) ;

        $sitesPossessedByCustomer = $customerSiteRepo->getSitesByUserAndCustomer( $user, $customer ) ;

        $sitesNotAllowed = $sitesSelected->filter( function( Site $site ) use ( $sitesPossessedByCustomer ) {
            return !in_array($site , $sitesPossessedByCustomer ) ;
        } ) ;

        if( $sitesNotAllowed->count() < 1 ){ return true ; }
        else{

            $this->_flashBagHandler->addErrorInFlashbag( new NotAllowedSitesError() );
            return false ;
        }
    }

}