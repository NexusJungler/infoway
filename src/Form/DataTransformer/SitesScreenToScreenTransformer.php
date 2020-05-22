<?php

// src/Form/DataTransformer/IssueToNumberTransformer.php
namespace App\Form\DataTransformer;

use App\Entity\Admin\Screen;
use App\Entity\Customer\LocalProgramming;
use App\Entity\Customer\Media;
use App\Entity\Customer\Site;
use App\Entity\Customer\SiteScreen;
use App\Repository\Customer\SiteScreenRepository;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SitesScreenToScreenTransformer implements DataTransformerInterface
{
    private $entityManager;
    private ArrayCollection $screens ;
    private SiteScreenRepository $siteScreenRepo ;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager('kfc');
        $this->siteScreenRepo =   $this->entityManager->getRepository(SiteScreen::class) ;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Media|null $issue
     * @return ArrayCollection
     */
    public function transform( $screens )
    {
        $screensidsToImport = [] ;

        $this->screens = $screens ;
        $importatedSiteScreensSortedById = [] ;


        foreach( $this->screens as $screen ){
            if( ! $screen instanceof Screen ) continue ;
            if( is_int ( $screen->getId() ) && !in_array ( $screen->getId() ,$screensidsToImport ) ) $screensidsToImport[] = $screen->getId() ;
        }

        $importatedSiteScreens = $this->siteScreenRepo->findBy( ['id' => $screensidsToImport ] ) ;

        foreach( $importatedSiteScreens as $importedSiteScreen ){
            $importatedSiteScreensSortedById[ $importatedSiteScreens->getId() ] = $importatedSiteScreens ;
        }


         return $this->screens->map( function( Screen $screen ) use ( $importatedSiteScreensSortedById ){

             if(  isset( $importatedSiteScreensSortedById[ $screen->getId() ] ) ) { return $importatedSiteScreensSortedById[ $screen->getId() ] ; }
             else {

                 $createdSiteScreen = new SiteScreen();
                 $createdSiteScreen->setScreenId( $screen->getId() );
                 $screen->getSite()->addSiteScreen( $createdSiteScreen );
//                 $createdSiteScreen->setSite( $screen->getSite() ) ;

                 return $createdSiteScreen ;
             }
        } ) ;
    }


    public function reverseTransform( $siteScreens )
    {

        return $siteScreens->map( function( SiteScreen $siteScreen){
            return $siteScreen->getScreen() ;
        } ) ;


//        if (null === $site) {
//// causes a validation error
//// this message is not shown to the user
//// see the invalid_message option
//            throw new TransformationFailedException(sprintf(
//                'An issue with number "%s" does not exist!',
//                $site
//            ));
//        }
//
//        return $site;
    }
}