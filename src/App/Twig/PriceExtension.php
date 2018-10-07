<?php

/**
 * @author Guillaume Pédelagrabe <gpedelagrabe@gmail.com>
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'price'])
        ];
    }

    /**
     * @param int $amount
     * @return string
     */
    public function price(int $amount): string
    {
        return number_format(($amount/100), 2, ',', ' ').' €';
    }

}