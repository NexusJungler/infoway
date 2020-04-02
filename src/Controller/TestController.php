<?php

namespace App\Controller;

use App\Entity\Admin\Country;
use App\Entity\Admin\Customer;
use App\Entity\Admin\Feature;
use App\Entity\Admin\Perimeter;
use App\Entity\Admin\Permission;
use App\Entity\Admin\TimeZone;
use App\Entity\Admin\User;
use App\Entity\Customer\Role;
use App\Entity\Customer\RolePermissions;
use App\Entity\Customer\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/testroute", name="test")
     */
    public function index()
    {

        $em =  $this->getDoctrine()->getManager();


        $userRepo = $em->getRepository(User::class);
        $customerRepo = $em->getRepository(Customer::class);
        $allUsers = $userRepo->findAll();

        dd($userRepo->getUserWithSitesById(1));
        $allSitesNeededFilteredByCustomersArray=[];

        foreach($allUsers as $user){


            foreach($user->getSitesIds() as $site) {
                $siteCustomer = $site->getCustomer();
                $siteCustomerName = $siteCustomer->getName();

                if( !array_key_exists( $siteCustomerName , $allSitesNeededFilteredByCustomersArray ) )$allSitesNeededFilteredByCustomersArray[ $siteCustomerName ] = [] ;
                $allSitesNeededFilteredByCustomersArray[ $siteCustomerName ][] = $site->getSiteId();
            };
        }

        $allCustomers = $customerRepo->findBy(['name' => array_keys($allSitesNeededFilteredByCustomersArray)]);
        $allCustomersIndexedByName = [];

       foreach($allCustomers as $customer){
           $allCustomersIndexedByName[$customer->getName()] = $customer ;
       }

        foreach( $allSitesNeededFilteredByCustomersArray as $enseigne => $site ){

            if (!array_key_exists($enseigne,$allCustomersIndexedByName) || ! $allCustomersIndexedByName[$enseigne] instanceof Customer){
              continue ;
            }

            $enseigneEntity = $allCustomersIndexedByName[ $enseigne ];

            $siteRepo  = $this->getDoctrine()->getRepository(Site::class, $enseigne );
            $userSites = $siteRepo->findBy(['id'=> $site]);

            foreach($userSites as $userSite){
                $userSite->setCustomer($enseigneEntity);
                $user->addSite($userSite);
            }
        }

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
