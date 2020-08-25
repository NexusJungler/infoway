<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Customer\Media;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class GenerateMediaElementExtension extends AbstractExtension
{
public function getFunctions()
{
return [
new TwigFunction('mediaElement', [ $this, 'generateMediaElement' ]),
];
}

public function generateMediaElement(Media $media)
{

    $mediaNamespace = explode('\\',get_class( $media ) ) ;
    $instanceName = end( $mediaNamespace ) ;

    switch( $instanceName ) {

        case 'Image' : $domElement =  '<img src=\'\build\medias\images\low\\'. $media->getName() . '.' . $media->getExtension() .'\' >' ;
        break;
        default : return '';
    }

    return new Markup( $domElement, 'UTF-8' );

}
}