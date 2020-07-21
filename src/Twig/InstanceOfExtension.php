<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class InstanceOfExtension extends AbstractExtension
{
public function getFilters()
{
return [
new TwigFilter('instanceof', [$this, 'isInstanceOf']),
];
}

public function isInstanceOf($var , $instance)
{

return $var instanceof $instance;
}
}

