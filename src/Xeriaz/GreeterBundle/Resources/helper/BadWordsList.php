<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Resources\helper;

class BadWordsList
{
    /**
     * @return string[]
     */
    public function getBadWords(): array
    {
        return [
            'bloody',
            'arse',
            'prick',
            'poop'
        ];
    }
}
