<?php


namespace App\Service;


use App\Entity\Admin\ThematicTheme;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class VideoThematicInfosHandler
{

    private ManagerRegistry $__managerRegistry;

    private ObjectManager $__defaultManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->__managerRegistry = $managerRegistry;
        $this->__defaultManager = $managerRegistry->getManager('default');
    }

    public function retrieveVideosThematics(array &$videos)
    {

        foreach ($videos['medias'] as &$video)
        {

            if(null !== $video['theme'] && $video['theme'] > 0)
            {
                $thematic = $this->__defaultManager->getRepository(ThematicTheme::class)->find($video['theme']);
                $video['theme'] = $thematic;
            }
            else
                $video['theme'] = null;

        }

        //dd($videos);

        return $videos;

    }

    public function getThematicByThemacticId(int $thematicId)
    {
        $thematic = $this->__defaultManager->getRepository(ThematicTheme::class)->find($thematicId);
        dd( $thematic );
    }

}