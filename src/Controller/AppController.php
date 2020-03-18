<?php


namespace App\Controller;


use App\Entity\Admin\{Country, Role};
use App\Entity\Customer\{CompanyPiece, CompanyPieceType, Media};
use App\Form\CreateCompanyPieceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class AppController extends AbstractController
{



    /**
     *
     * @Route("/", name="app")
     * @Route("/template", name="app::home")
     *
     * @param Request $request
     * @return Response
     */
    public function homePage(Request $request): Response
    {

//        if($this->getUser() === null)
//            return $this->redirectToRoute("user::login");

        $customer = [
            'ARES',
            'Q087',
            'AEAS',
            'Q087A2',
            'ARAS',
            'Q08'
        ];

        $location = (object) [
            'city' => 'Paris',
            'timezone' => 'Europe/Paris',
            'date_format' => 'd-m-Y',
            'clock_format' => 24
        ];

        //dump($location);

        return $this->render("home.html.twig", [
            'customer' => $customer,
            'location' => $location
        ]);
    }





    /**
     * @Route(path="/setting", name="app:settings")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function settings(Request $request): Response
    {
        $customer = [
            'ARES',
            'Q087',
            'AEAS',
            'Q087A2',
            'ARAS',
            'Q08'
        ];

        $roles = [

        ];

        $subdivision = new CompanyPiece();

        $currentUserDatabaseManager = 'quick'; // dynamic

        $customerEm = $this->getDoctrine()->getManager($currentUserDatabaseManager);
        $adminEm = $this->getDoctrine()->getManager();

        $companyPieceTypeRep = $customerEm->getRepository( CompanyPieceType::class)->setEntityManager($customerEm);
        $countryRep = $adminEm->getRepository(Country::class);
        $roleRep = $adminEm->getRepository(Role::class);

        $allCompanyPieceTypeLevel1 = $companyPieceTypeRep->findWhereLevelGreaterThan(1);


        $form = $this->createForm( CreateCompanyPieceType::class, $subdivision);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // içi controller le type reçu avant de faire la requete pour le select
            $companyPieceTypeId = intval($request->request->get('type'));
            $countryId = intval($request->request->get('country'));


            $type = $companyPieceTypeRep->findOneById($companyPieceTypeId);
            if(!$type)
                throw new \Exception(sprintf("Internal error : cannot find CompanyPieceType with id '%d' !", $companyPieceTypeId));


            $country = $countryRep->findOneById($countryId);
            if(!$country)
                throw new \Exception(sprintf("Internal error : cannot find country with id '%d' !", $countryId));


            $logoFile = $form->get('file')->getData();

            /*$finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $logoFile);
            $splash = explode('/', $mimeType);
            $real_file_extension = $splash[1];

            if(!in_array($real_file_extension, $this->getParameter("logoAuthorizedMimeTypes")))
                echo "error";*/

            $newFileName = $subdivision->getName().'.'.$logoFile->guessExtension();

            try {
                $logoFile->move(
                    $this->getParameter('logoDirectory'),
                    $newFileName
                );
            }
            catch (FileException $e) {
                dd($e->getMessage());
            }

            $subdivision->setType($type)
                        ->setCountry($country->getId())
                        ->setLogoName($newFileName);

            dd($subdivision, $request->request);

            $customerEm->persist($subdivision);
            $customerEm->flush();

            echo "submitted";

            dd($subdivision);

        }


        dump($allCompanyPieceTypeLevel1, $subdivision);

        return $this->render("setting.html.twig", [
            'customer' => $customer,
            'form' => $form->createView(),
            'authorizedCompanyPieces' => $allCompanyPieceTypeLevel1,
            'countries' => $countryRep->findAll(),
            'roles' => $roleRep->findAll(),
        ]);

    }

    /**
     * @Route(path="/products", name="app:products")
     *
     * @param Request $request
     * @return Response
     */
    public function products(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("products.html.twig", [
            'customer' => $customer
        ]);

    }

    
    /**
     * @Route(path="/site", name="app:site")
     *
     * @param Request $request
     * @return Response
     */
    public function site(Request $request): Response
    {
        $customer = [
            'nom',
            'format',
            'categorie',
            'description',
            'tags',
            'Q08'
        ];
        return $this->render("site.html.twig", [
            'customer' => $customer
        ]);

    }

}