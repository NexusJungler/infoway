<?php


namespace App\TwigExtension;


use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class StringToIntExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('string_to_int', [$this, 'stringToInt']),
        ];
    }

    /**
     * Renvoie la valeur numérique d'une string
     *
     * @param string $number
     * @return int
     */
    public function stringToInt(string $number)
    {
        return intval($number);
    }

}