<?php

declare(strict_types=1);

namespace App\Service;

class GreeterService
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function greet(string $name): string
    {
        return "Hello {$name}";
    }
}
