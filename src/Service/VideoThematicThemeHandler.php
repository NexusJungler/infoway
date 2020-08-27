<?php


namespace App\Service;


use App\Entity\Admin\ThematicTheme;
use App\Entity\Admin\VideoThematicThematicTheme;
use App\Entity\Customer\VideoThematic;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;

class VideoThematicThemeHandler
{

    public function __construct(ObjectManager $defaultManager)
    {
        $this->__thematicThemeRepo = $defaultManager->getRepository(ThematicTheme::class);
        $this->__videoThematicThematicThemeRepo = $defaultManager->getRepository(VideoThematicThematicTheme::class);
    }

    public function updateVideoThematicTheme(VideoThematic $videoThematic, ThematicTheme $thematicTheme): self
    {

        // si la thematique n'est pas associé à la video ou si aucune thematique n'est attribué
        if($videoThematic->getTheme() !== $thematicTheme->getId())
        {

            // si la video était déjà associé à une thematique (0 => pour que l'utilisatuer puisse attribuer une thematique ultérieurement)
            if($videoThematic->getTheme() > 0)
            {
                // on supprime la video de la collection de sa thematique precedente
                $videoThematicPreviousThematic = $this->__thematicThemeRepo->find($videoThematic->getTheme());
                if(!$videoThematicPreviousThematic)
                    throw new Exception(sprintf("No thematic foudn with id '%s'", $videoThematic->getTheme()));

                $videoThematicPreviousThematic->removeVideoThematic($videoThematic);

            }

            $thematicTheme->addVideoThematic($videoThematic);

        }

        return $this;

    }

}