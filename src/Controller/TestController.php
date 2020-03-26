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
        $allUsers = $userRepo->findAll();

        foreach($allUsers as $user){

            $userEnseignes = $user->getCustomers();

            foreach($userEnseignes as $enseigne){

                $customEm = $this->getDoctrine()->getManager($enseigne->getName());
                $siteRepo = $customEm->getRepository(Site::class,$enseigne->getName());

                $allSitesInCUrrentEnseigne = $siteRepo->findAll();

                foreach($allSitesInCUrrentEnseigne as $siteInCurrentEnseigne){
                    $enseigne->addSite($siteInCurrentEnseigne) ;
                }
            }
            foreach($userEnseignes as $enseigne){
              dump($enseigne->getSites());
            }
        }
        dd($allUsers);
//       // $customerRepo->findUserEnseignesAndSitesByUserName('test');
//      //  $customerRepo->findCustomerWithSiteByName('kfc') ;
//        $role = $em->getRepository(Role::class)->findOneById(1);
//
//        $rolePermission = new RolePermissions() ;
//        $rolePermission->setRole($role);
//        $rolePermission->setPermissionId(1);
//
//        $em->persist($rolePermission);
//        $em->flush();

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
