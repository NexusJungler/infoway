<?php


namespace App\TwigExtension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateDiffExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('get_date_diff_from_now', [$this, 'getDateDiffFromNow']),
        ];
    }

    /**
     * Retourne le nombre de jours de différence entre la date donnée et la date actuelle
     *
     * @param string $date
     * @return string
     * @throws \Exception
     */
    public function getDateDiffFromNow(string $date)
    {

        $date = new \DateTime($date);
        $now = new \DateTime();


        if($date < $now)
            return intval($date->diff($now)->format("-%a"));

        return intval($date->diff($now)->format("%a"));

    }

}