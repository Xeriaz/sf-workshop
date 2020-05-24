<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Resources\helper;

class GreetWordsList
{
    /**
     * @return string[]
     */
    public function getGreetWords(): array
    {
        return [
            'Hello',
            'Hi',
            'Greetings',
            'Yo'
        ];
    }
}
