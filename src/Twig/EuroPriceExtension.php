<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EuroPriceExtension extends AbstractExtension
{
    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('eurPrice', [$this, 'formatPrice']),
        ];
    }

    /**
     * @param float $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    public function formatPrice(
        float $number,
        int $decimals = 0,
        string $decPoint = '.',
        string $thousandsSep = ','
    ): string {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = $price . '€';

        return $price;
    }
}
