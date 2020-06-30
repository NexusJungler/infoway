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

public function addClassToElement( string $element, string $class ){
    $element = rtrim( $element , '>');
    $element .= 'class="'.$class .'" >';
    return $element ;
}
public function generateMediaElement( Media $media, ?array $options=null )
{

    if(isset( $options ) && ! is_array( $options )  ) throw new \Error('invalid value for option');
    if(isset( $options['attr'] ) && ! is_array( $options['attr'] ) ) throw new \Error('invalid value for attr');


    $mediaNamespace = explode('\\',get_class( $media ) ) ;
    $instanceName = end( $mediaNamespace ) ;

    switch( $instanceName ) {

        case 'Image' : $domElement =  '<img src=\'\build\medias\images\low\\'. $media->getName() . '.' . $media->getExtension() .'\' >' ;
        break;
        default : return '';
    }

    if(isset( $options['attr'] ) &&  isset( $options['attr']['class'] ) ){
        if( ! is_string( $options['attr']['class'] ) )throw new \Error('invalid value for class');
        $domElement = $this->addClassToElement( $domElement, $options['attr']['class'] ) ;
    }

    return new Markup( $domElement, 'UTF-8' );

}
}