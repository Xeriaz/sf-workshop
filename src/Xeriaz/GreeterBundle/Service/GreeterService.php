<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Service;

class GreeterService
{
    private $greetingWords = [
        'Hello',
        'Hi',
        'Greetings',
        'Yo'
    ];

    /**
     * @param string $name
     *
     * @return string
     */
    public function greet(string $name): string
    {
        return "{$this->greetingWords[\rand(0, 3)]} {$name}";
    }
}
