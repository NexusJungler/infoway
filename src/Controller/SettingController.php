<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Admin\Country;
use App\Entity\Customer\CompanyPiece;
use App\Entity\Customer\CompanyPieceType;
use App\Form\CreateCompanyPieceType;

class SettingController extends AbstractController
{
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

        $subdivision = new CompanyPiece();
        $permission = 1;
        $currentUserDatabaseManager = 'Kfc'; // must be dynamic

        $adminEm = $this->getDoctrine()->getManager();
        $customerEm = $this->getDoctrine()->getManager($currentUserDatabaseManager);

        $typeRep = $customerEm->getRepository( CompanyPieceType::class);
        $pieceRep = $customerEm->getRepository( CompanyPiece::class);
        $countryRep = $adminEm->getRepository(Country::class);

        $allPieces = $pieceRep->findBy([], ['type' => 'desc']);
        $allPiecesOrderBy = [];

        foreach ($allPieces as $piece) {
            $type = $piece->getType()->getId();
            // dd($type);
            if(!isset($allPiecesOrderBy[$type])) {
                $allPiecesOrderBy[$type] = [];
            }
            $allPiecesOrderBy[$type][] = $piece;
        }

        $form = $this->createForm( CreateCompanyPieceType::class, $subdivision, ['action' => $this->generateUrl('app:settings')]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // içi controller le type reçu avant de faire la requete pour le select
            $companyPieceTypeId = intval($request->request->get('type'));
            $countryId = intval($request->request->get('country'));


            $type = $customerEm->getRepository(CompanyPieceType::class)->findOneById($companyPieceTypeId);
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

        dump($allPiecesOrderBy);
        return $this->render("setting.html.twig", [
            'customer' => $customer,
            'form' => $form->createView(),
            'authorizedCompanyPieceTypes' => $typeRep->findByLevel($permission),
            'CompanyPieces' => $allPiecesOrderBy,
            'countries' => $countryRep->findAll()
        ]);

    }

}