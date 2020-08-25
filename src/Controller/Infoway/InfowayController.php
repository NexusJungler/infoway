<?php


namespace App\Controller\Infoway;


use App\Entity\Customer\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/infoway")
 */
class InfowayController extends AbstractController
{

    /**
     * @Route(path="/media/encodage", name="infoway::mediaEncodage", methods={"GET","POST"})
     */
    public function mediaEncodage(Request $request)
    {

        $allMedias = [];

        // get all customers medias
        foreach ($this->getDoctrine()->getManagerNames() as $key => $value)
        {

            if($key !== 'default')
            {

                $manager = $this->getDoctrine()->getManager($key);
                $allMedias[] = $manager->getRepository(Media::class)->setEntityManager($manager)->getAllMediasStillUsedInApp();

            }

        }

        return $this->render("infoway/media_encodage.scss", [
            'allMedias' => $allMedias
        ]);


    }

}