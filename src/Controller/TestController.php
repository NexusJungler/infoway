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

//Creer pour effectuer des tests
class TestController extends AbstractController
{
    /**
     * @Route("/testroute", name="test")
     */
    public function index()
    {

        //recuperation du manager
        $em =  $this->getDoctrine()->getManager();

//recuperation des repository admin  User et Customer
        $userRepo = $em->getRepository(User::class);
        $customerRepo = $em->getRepository(Customer::class);

        //On recup tous les users
        $allUsers = $userRepo->findAll();


        //creaion d'un array $allSitesNeededFilteredByCustomersArray qui contiendra les sites avec les noms des enseignes en clé de tableau afin de realiser une seule connection par enseigne plus tard
        $allSitesNeededFilteredByCustomersArray=[];

        //boucle sur tous les users
        foreach($allUsers as $user){


            //on recupere les id des sites des users dans la base admin et on boucle dessus
            foreach($user->getSitesIds() as $site) {
                //on extrait l'object enseigne lié au site et son  nom
                $siteCustomer = $site->getCustomer();
                $siteCustomerName = $siteCustomer->getName();

                //Si aucune clé representant l enseigne du site en cour n existe dans le tableau on la créé et on lui affecte un tableau qui contiendra tous les sites
                if( !array_key_exists( $siteCustomerName , $allSitesNeededFilteredByCustomersArray ) )$allSitesNeededFilteredByCustomersArray[ $siteCustomerName ] = [] ;

                //on push le site en cour dans le tableau representant son enseigne
                $allSitesNeededFilteredByCustomersArray[ $siteCustomerName ][] = $site->getSiteId();
            };
        }

        //On va chercher toutes les enseignes qui apparaissent dans le tableau des sites
        $allCustomers = $customerRepo->findBy(['name' => array_keys($allSitesNeededFilteredByCustomersArray)]);

        //on cree un tableau ou on placera les enseignes recuperées de la base.
        $allCustomersIndexedByName = [];

        //on place les objets enseignes recuperés depuis la base dans un nouveau tableau qui contiendra leur nom en tant que clé.
       foreach($allCustomers as $customer){
           $allCustomersIndexedByName[$customer->getName()] = $customer ;
       }

       //boucle sur le premier tableau qui contient les sites
        foreach( $allSitesNeededFilteredByCustomersArray as $enseigne => $site ){

            //si on trouve pas le nom de l enseigne en cour dans les clés de  tableau des enseignes recuperé depuis la base ou que  la valeur attribuée n est pas de type Customer on passe à l'iteration suviante
            if (!array_key_exists($enseigne,$allCustomersIndexedByName) || ! $allCustomersIndexedByName[$enseigne] instanceof Customer){
              continue ;
            }

            //on store l'objet customer dans une variable
            $enseigneEntity = $allCustomersIndexedByName[ $enseigne ];

            //recuperation du repository des sites dans l enseigne en cour
            $siteRepo  = $this->getDoctrine()->getRepository(Site::class, $enseigne );
            //on va chercher le site par son id
            $userSites = $siteRepo->findBy(['id'=> $site]);

            foreach($userSites as $userSite){
                //on lui attribue son customer et on l'ajoute a l objet user
                $userSite->setCustomer($enseigneEntity);
                $user->addSite($userSite);
            }
        }

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
