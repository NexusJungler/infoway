<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;

//class_exists('Twig\Extension\EscaperExtension');
use Twig\TwigFilter;

class CustomerEscaperExtension extends AbstractExtension
{

    private $env ;
    public function __construct(Environment $env)
    {
        $this->env = $env ;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('escapewithoutprototype', [$this, 'escapeWithoutPrototype'], ['pre_escape' => 'html']),
        ];
    }

    public function escapeWithoutPrototype(  $html  )
    {
        dd($html);
        $filterFunc =  $this->env->getFilters()['escape']->getCallable() ;
        dd(call_user_func($filterFunc, $this->env, $html, 'html_attr'));



     return $filters ;
    }
}

