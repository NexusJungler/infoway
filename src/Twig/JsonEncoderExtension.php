<?php

namespace App\Twig;

use App\Entity\Admin\Entity;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonEncoderExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('encodeIds', [ $this, 'encodeIds' ]),
        ];
    }

    public function encodeIds(Collection $entities )
    {
        $idsArray = $entities->filter( fn( $object ) => is_object( $object ) && method_exists( $object, 'getId' ) )->map( fn( $object ) => $object->getId() )->toArray() ;

        return json_encode( $idsArray );
    }
}

