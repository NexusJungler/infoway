<?php

namespace App\Repository\Admin;

use App\Entity\Admin\ThematicTheme;
use App\Entity\Admin\VideoThematicThematicTheme;
use App\Entity\Customer\VideoThematic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * @method VideoThematicThematicTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideoThematicThematicTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideoThematicThematicTheme[]    findAll()
 * @method VideoThematicThematicTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoThematicThematicThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoThematicThematicTheme::class);
    }

    public function updateVideoThematicTheme(VideoThematic &$videoThematic, ThematicTheme &$thematicTheme): self
    {

        // si la thematique n'est pas associé à la video ou si aucune thematique n'est attribué
        if($videoThematic->getTheme() !== $thematicTheme->getId())
        {

            // on supprime l'ancienne relation si elle existe
            $videoThematicPreviousThematic = $this->_em->createQueryBuilder()->select("v")
                                                       ->distinct()
                                                       ->from(VideoThematicThematicTheme::class, "v")
                                                       ->where("v.videoThematicId = :videoThematicId")
                                                       ->setParameter('videoThematicId', $videoThematic->getId())
                                                       ->getQuery()
                                                       ->getOneOrNullResult();

            if($videoThematicPreviousThematic)
            {
                $this->_em->remove($videoThematicPreviousThematic);
                $this->_em->flush();
            }

            // on vérifie que la nouvelle thematique attribuée existe
            /* $newThematicExist = $this->_em->getRepository(ThematicTheme::class)->find($videoThematic->getTheme());
            if(!$newThematicExist)
                throw new Exception(sprintf("No thematic found with id '%s'", $videoThematic->getTheme()));*/

            $videoThematicThematicTheme = new VideoThematicThematicTheme();
            $videoThematicThematicTheme->setVideoThematicId($videoThematic->getId() );

            $thematicTheme->addVideoThematic($videoThematicThematicTheme);

            $videoThematic->setTheme( $thematicTheme->getId() );

            $this->_em->persist($videoThematicThematicTheme);

            $this->_em->flush();

            //dd($thematicTheme);

        }

        return $this;

    }

    // /**
    //  * @return VideoThematicTheme[] Returns an array of VideoThematicTheme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VideoThematicTheme
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
