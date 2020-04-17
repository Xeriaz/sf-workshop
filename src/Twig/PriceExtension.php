<?php declare(strict_types = 1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * TODO: make it so,
 */
class PriceExtension extends AbstractExtension
{
	public function getFilters()
	{
		return [
			new TwigFilter('price', [$this, 'formatPrice']),
		];
	}

	public function formatPrice($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
	{
		$price = number_format($number, $decimals, $decPoint, $thousandsSep);
		$price = '$'.$price;

		return $price;
	}
}
